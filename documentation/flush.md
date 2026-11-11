# The flush

`EntityManager::flush()` writes everything that changed since the last flush: new entities (`persist()`), changes to
tracked entities, and deletions (`delete()`). It finds the changes by diffing each tracked entity against the snapshot
taken when it was loaded or last written, so a loaded entity that you change needs no `persist()`.

## Rounds

One flush can take several writes, called rounds:

1. The changes are gathered. Before-flush handlers (`BeforeFlushHandler`) may still change entities; when one says it
   did, the changes are gathered again.
2. The changes are written, through the storage's `Flusher`. One write is one transaction.
3. The written entities become the new baseline: their snapshots are refreshed and an entity that was deleted leaves
   the identity map.
4. The after-flush handlers (`AfterFlushHandler`) react to what was written. The change log and the entity events
   (`EntityCreated`, `EntityChanged`, `EntityDeleted`, and their listeners) work this way.

When a handler answers `true` from `handle()`, it may have created or changed entities, also through the listeners of
the events it dispatched, and the flush goes back to step 1. Whatever it created **or changed**, also on entities that
were already stored, is written in a next round of the same flush. The flush ends when the handlers answer `false`, or
when a round finds nothing to write. A handler that answers `false` is taken at its word: what it changed anyway waits
for the next flush.

A flush in which nothing changed writes nothing and calls no after-flush handler, so a read-only request costs only
the diff.

When handlers keep changing entities in every round, the flush gives up after 100 rounds with `FlushDoesNotSettle`,
which names the classes changed last.

## A listener persists, it does not flush

A listener of an entity event, or any other code an after-flush handler runs, creates and changes entities and leaves
the writing to the flush it runs in:

```php
#[EventListener]
public function onConfirmed(TermConfirmed $event): void
{
    $charge = $this->entityManager->create(Charge::class, [...]);

    $this->entityManager->persist($charge);

    // No flush(): the flush that confirmed the term writes the charge in its next round
}
```

A listener still flushes itself when:

- it has to read its own writes back from storage straight away, for example a duplicate check with a query;
- it also runs outside any flush, for example from a console command, where nothing else would write its changes.

A `flush()` called while a flush is in progress only writes; see below for what it leaves to the outer flush.

## Work after the flush: `afterFlush()`

Some work belongs to the unit of work but reads from storage what that unit of work changed: a listener that queries
entities its own flush may not have written yet. A listener runs in the middle of a flush, so it cannot do that work
itself. It registers it instead:

```php
$this->entityManager->afterFlush(
    fn() => $this->provision($subscription),
    'provision:' . $subscription->id,
);
```

- The callback runs at the end of the **outermost** flush in progress, once every round has been written, or at the
  end of the next flush when none is in progress. A flush started inside another one leaves the callbacks to the
  outer one.
- What the callback creates or changes is written by that same flush, so the work is stored when the flush returns.
- A key given twice before the callbacks run keeps the first callback only, so work asked for by several changes runs
  once. Without a key every callback is one of its own.
- A callback may register further callbacks; they run in the next round, after what the earlier callbacks changed has
  been written. When callbacks keep asking for more, the flush gives up after 100 rounds with
  `AfterFlushCallbacksDoNotSettle`, which names their keys.
- When the flush fails, its pending callbacks are dropped: they belonged to a unit of work that was not stored, and must
  not run on the next flush.

Use the task queue instead for work that may run later and may need a retry, such as sending an e-mail.

## When a write fails

A write that throws, such as a duplicate key, was rolled back. Its entities keep the state they had before it: a new
entity is still new, a change is still pending, a deletion is still to be done, and the next flush writes them again.
To drop a new entity whose insert failed, call `discard()`; `Repository::fetchOrCreate()` does so when it loses a race
for the same row, and returns the row that won.

Rounds of the same flush that were written before the failing one stay written.

## Entities that are not diffed

Diffing costs a snapshot comparison per tracked entity on every flush. Three kinds of entity skip it:

- **Fetched read-only.** `get($class, $id, trackChanges: false)`, and the same argument on the `Repository` fetch
  methods, keep an entity in the identity map without a snapshot. Use it for lookups you won't change.
- **Not loaded.** A relation that has not been followed is a lazy ghost and cannot have changed; see
  [Relations](relations).
- **Append-only.** An entity class marked `#[AppendOnly]`, such as a log row, is written once. After its insert, and
  when it is fetched with `get()`, it is no longer tracked: a later change to it is not written, but deleting it still
  works. An append-only entity whose insert failed is still tracked, so the next flush inserts it.

## Fetching one row

`Repository::fetchOne()`, and `fetchById()`, `getOrCreate()` and `fetchOrCreate()` through it, wrap the selector in
`FirstOf`, so the storage stops at the first matching row instead of returning every match. `FirstOf` is not cacheable,
as every `FirstOf` shares one class whatever selector it wraps.

## Known gaps

- There is no transaction across the rounds of one flush: when a later round fails, the earlier rounds stay written.
