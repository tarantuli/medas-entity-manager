# Ownership filters

An ownership filter restricts which entities of a class a caller gets to see in a list, based on who the caller is.
Typical use: a partner sees only the orders of its own tenants, an account sees only its own carts.

The filter is added to the query itself, before it runs. Paging and the total count therefore only ever see rows the
caller may read: there is no page of 25 with 3 visible rows, and no total that gives away how many rows other callers
have. It does not replace authorization; keep the read vote as a second check.

## Adding a filter to an entity

Tag the entity class with @class(Medas\EntityManager\Attributes\AddOwnershipFilter) and name one or more filter
classes:

```php
#[Entity('orders')]
#[AddOwnershipFilter(TenantOwnershipFilter::class)]
class Order implements HasId
{
    #[Id, IsGeneratedValue]
    public Uuid $id;

    public Tenant $tenant;
}
```

The metadata compiler stores the class names in `MetaData::$ownershipFilters` (an empty array when the attribute is
absent). With more than one filter, they are applied in the order given, each receiving the result of the one before.

## Writing a filter

A filter is a service that implements @class(Medas\EntityManager\Interfaces\OwnershipFilter):

```php
interface OwnershipFilter
{
    public function addFilters(array $filters): array;
}
```

`addFilters()` receives the filters of the request and returns them with its own restriction added. It is resolved with
`service()`, so it must be a `#[Service]`, and it may take any services or config values in its constructor.

```php
#[Service]
readonly class AccountOwnershipFilter implements OwnershipFilter
{
    public function __construct(
        private RequestFactory $requestFactory,
    )
    {
    }

    public function addFilters(array $filters): array
    {
        $account = $this->currentAccount();

        // Code that runs for nobody in particular is not held back.
        if ($account === null) {
            return $filters;
        }

        $filters['account'] = (string) $account->id();

        return $filters;
    }
}
```

### The shape of `$filters`

The array is in the REST filter syntax of the rest-request-handler: the same array that is built from the query string
of a collection request, such as `GET /orders?tenant=…&page=2`. Keys are filter names, values are strings. Every entry
must hold at once (they are AND-ed).

| Key                        | Value              | Meaning                                                     |
|----------------------------|--------------------|-------------------------------------------------------------|
| `property`                 | `value`            | property equals value                                       |
| `property` + operator      | `value`            | compare with one of the comparison operators (see below)    |
| `property∈`                | `id1,id2,…`        | property is one of a comma-separated list                   |
| `storeName.property`       | `value`            | a property of a related entity, reached through its store   |
| `storeName.property∈`      | `id1,id2,…`        | the same, with an operator                                  |
| `id`                       | `value`            | the entity's own id, for a filter on the owner entity itself |

Notes on these shapes:

- **Operators are configurable.** The suffixes (`∈`, `∉`, `!`, `>`, `<`, `*`, `^`, `$`, …) are config options of the
  rest-request-handler (`comparison-operators` group). Don't hard-code them; inject the one you need, for example
  `#[ConfigValue(IsInOperator::class)] private string $isInOperator`, and build the key as
  `$property . $this->isInOperator`.
- **Relations use the store name.** A dotted key names the related entity by its store name (the name in its
  `#[Entity]` attribute), followed by one property of it: a `CartItem` reaches its account with `carts.account`, a
  `SubscriptionTerm` its tenant with `subscriptions.tenant`. One level deep only.
- **Relation and id values are ids as text.** A uuid is given in its text form; the selector builder converts it to the
  property's id type.
- **Lists can't be empty.** `∈` needs at least one value. To match nothing, use a value no row has, such as the nil uuid
  `00000000-0000-0000-0000-000000000000`.
- **Other keys pass through.** The array also holds `page`, `perPage`, sorting and anything else of the request. Leave
  keys you don't own untouched.

### Narrow, never widen

The caller may already have sent a filter under the same key. Replacing it blindly would let the caller see less than
asked for, which is harmless; keeping it blindly would let the caller ask for someone else's rows. Merge so the result
can only narrow:

- For a single value: keep the caller's value only when it is the allowed one, otherwise use a value that matches
  nothing.

  ```php
  $filters['account'] = !isset($filters['account']) || $filters['account'] === $accountId
      ? $accountId
      : self::NO_ACCOUNT;
  ```

- For a list: intersect the caller's list with the allowed list.

  ```php
  $key = 'tenant' . $this->isInOperator;

  if (isset($filters[$key])) {
      $allowedIds = array_intersect($allowedIds, explode(',', (string) $filters[$key]));
  }

  $filters[$key] = implode(',', $allowedIds) ?: self::NO_TENANT;
  ```

Filters under other keys need no such care: since all filters must hold at once, they can only narrow further.

### No restriction

Return `$filters` unchanged when there is nothing to restrict: when no one is signed in (a console command, a background
task, a test without a request; getting the request may throw outside a web request, so catch that), or when the caller
may see everything, such as an admin.

### One class per property

A filter usually exists in several variants that differ only in which property holds the owner (`account`, `id`,
`carts.account`). Put the logic in an abstract base class with an abstract method returning the property, and add one
small `#[Service]` class per variant. The base class itself can't be the service: the service manager does not accept a
service that extends another service.

```php
abstract readonly class SignedInAccountFilter implements OwnershipFilter
{
    abstract protected function accountProperty(): string;

    // ...addFilters() uses $this->accountProperty() as the key
}

#[Service]
readonly class CartAccountOwnershipFilter extends SignedInAccountFilter
{
    protected function accountProperty(): string
    {
        return 'carts.account';
    }
}
```

## Where filters are applied

@class(Medas\EntityManager\Filters\OwnershipFilterApplier) runs the filters of an entity's metadata in turn. It is
called in one place: the rest-request-handler's `SelectorBuilder::build()`, which builds the query of a collection
request (`GetCollectionHandler`). Ownership filters apply only where user input is filtered: the REST collection
endpoints, and helpers or services that build their queries the same way.

They are deliberately **not** applied:

- When fetching by id (`EntityManager::get()`, the storage manager's `fetchOne()`), and so not on following a
  relation either. A fetch by id has no request context, and internal code must not depend on who happens to be signed
  in. Opening a single entity over REST is guarded by the read vote instead.
- In queries built with selectors in code. Code that should be restricted can call `OwnershipFilterApplier::apply()`
  itself, but the result is REST filter syntax, so it only works with the REST `SelectorBuilder`.
