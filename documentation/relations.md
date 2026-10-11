# Relations

## Relations to a single entity

A property typed with an entity class is a relation; the storage manager stores the id of the related entity.

```php
#[Entity('books')]
class Book implements HasId
{
    #[Id, IsGeneratedValue]
    public int $id;

    public string $title;

    public Author $author;
}
```

### Lazy loading

When a book is loaded, its author is not. The `author` property holds a lazy object (a PHP 8.4 lazy ghost) of the
`Author` class with only its id set:

- Reading the id (`$book->author->id`, or an `id()` method that only returns it) costs no query.
- Reading or writing any other property loads the author first, after which it behaves like any loaded entity: a
  write is picked up by the next `flush()`.
- The lazy object is registered in the identity map, so `EntityManager::get(Author::class, $id)` and other relations
  to the same author return the same instance.
- `flush()` skips authors that haven't been loaded, so it never loads them. Persisting a new book that refers to an
  author that hasn't been loaded, or deleting such an author, doesn't load it either.

This makes relations that are never followed free, and removes the cascade of queries that loading one entity used to
cause when its related entities had relations of their own.

Relations to an entity class that stores its original class (`#[StoreOriginalEntityType]`, for inheritance) are loaded
right away: the actual class has to be read from storage before an object of it can be created.

The lazy loading happens through the `FindEntity` event, which the storage layer dispatches for relation values. The
entity manager answers it with `EntityManager::reference()`, or with `EntityManager::get()` when the event's `$lazy`
argument (default `true`) is `false`.

## Many-to-many relations

In this example, we will connect one movie with zero or more genres.

The `Movie` class:

```php
#[Entity('movies')]
class Movie implements HasId
{
    #[Id, IsGeneratedValue]
    public int $id;

    #[IsUnique]
    public string $title;
}
```

The `Genre` class:

```php
#[Entity('genres')]
class Genre implements HasId
{
    #[Id, IsGeneratedValue]
    public int $id;

    #[IsUnique]
    public string $name;
}
```

We need a collection class, which can hold a number of `Genre` objects:

```php
/**
 * @extends RecordCollection<Genre>
 */
#[EntityCollection(Genre::class)]
class GenreCollection extends RecordCollection
{
}
```

It must be tagged with @class(Medas\EntityManager\Attributes\EntityCollection) and it must implement @class(
Medas\Core\Interfaces\ManagedCollection). @class(Medas\StorageManager\Entities\RecordCollection) is a good base class to
extend.

Simply use this collection class as the type of the `genres` property in `Movies`:

```php
#[Entity('movies')]
class Movie implements HasId
{
    #[Id, IsGeneratedValue]
    public int $id;

    #[IsUnique]
    public string $title;

    public GenreCollection $genres;
}
```

The storage manager will have enough information to generate the necessary migrations, and to store and fetch connected
genres.
