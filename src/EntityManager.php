<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{
    Attributes\EventListener,
    Attributes\Service,
    Events\BeforeResponse,
    Events\DebugInformation,
    Interfaces\EntityManager as EntityManagerInterface,
    Interfaces\TracksChanges
};

#[Service]
readonly class EntityManager implements EntityManagerInterface
{
    /**
     * How many times one flush() writes what its after-flush handlers changed, before it gives up. Only reached when
     * handlers keep reacting to each other's changes without end.
     */
    private const int MAX_FLUSH_ROUNDS = 100;

    public function __construct(
        private Entities\CollectionInitializer $collectionInitializer,
        private Entities\IdValue               $idValue,
        private Entities\Initializer           $initializer,
        private Entities\KeyMaker              $keyMaker,
        private Entities\ReferenceInitializer  $referenceInitializer,
        private Entities\UuidInitializer       $uuidInitializer,
        private FlushManager                   $flushManager,
        private MetaDataManager                $metaDataManager,
        private Snapshots\ChangeFinder         $changeFinder,
        private Snapshots\SnapshotManager      $snapshotManager,
        private EntityManagerContext           $context = new EntityManagerContext(),
    )
    {
    }

    public function autoPersistOnCreate(bool $value = true, bool $alsoFlush = true): void
    {
        $this->context->autoPersistOnCreate = $value;

        if ($value) {
            $this->autoFlushOnCreate($alsoFlush);
        }
    }

    public function autoFlushOnCreate(bool $value): void
    {
        $this->context->autoFlushOnCreate = $value;
    }

    public function setPurgingParameters(int|null $triggerSize, int|null $purgeAmount = null): void
    {
        $this->context->cachePurgeTriggerSize = $triggerSize;
        $this->context->cachePurgeAmount = $purgeAmount ?: (int) floor($triggerSize / 4);

        if ($this->context->cachePurgeAmount >= $this->context->cachePurgeTriggerSize) {
            throw new Exceptions\PurgeAmountShouldBeLessThanTriggerSize(
                $this->context->cachePurgeAmount,
                $this->context->cachePurgeTriggerSize
            );
        }
    }

    /**
     * The return value is an object of type `$className`.
     *
     * $trackChanges: false skips change-diffing for this entity entirely -- use it for a fetch
     * that's known to be read-only (an existence check, a lookup you won't mutate). The entity is
     * still returned from and cached in the identity map as usual, so a repeated get() for the
     * same id doesn't re-hydrate; it just never gets a saved-state snapshot, so it costs nothing
     * at flush() time no matter how many flushes happen while it's tracked.
     *
     * Tracking is one-way. On a cache hit for an entity that's already tracked, $trackChanges is
     * ignored -- once something is being diffed, this can't silently turn that back off and risk
     * dropping a real pending change. On a cache hit for a currently-untracked entity, passing
     * true here promotes it: a snapshot is taken right now as the new baseline, so anything about
     * it that changed *before* this call is invisible (there was nothing to compare against), but
     * anything changed from this point on is picked up normally.
     */
    /*
     * This is specified in PhpStorm in .phpstorm.meta.php
     */
    public function get(string $className, mixed $id, bool $trackChanges = true): object
    {
        $key = $this->keyMaker->get($className, $id);
        $this->context->entityAccessTime[$key] = hrtime(true);

        if (!array_key_exists($key, $this->context->entities)) {
            $this->doCircularDependencyCheck($className, $id, $key);

            try {
                $entity = $this->initializer->initializeAndHydrate($className, $id, $this);

                $this->updateCircularDependencyCheck($key);

                if ($trackChanges) {
                    $this->startTracking($entity);
                }
                else {
                    $this->context->untrackedEntities[$entity] = true;
                }

                $this->context->entities[$key] = $entity;

                ++$this->context->entityCount;
            }

            finally{
                // Always clean up, even on exception
                $this->updateCircularDependencyCheck($key);
            }

            if ($this->context->cachePurgeTriggerSize
                    && $this->context->entityCount >= $this->context->cachePurgeTriggerSize) {
                $this->purge();
            }
        }
        elseif ($trackChanges && isset($this->context->untrackedEntities[$this->context->entities[$key]])) {
            $entity = $this->context->entities[$key];

            unset($this->context->untrackedEntities[$entity]);

            $this->startTracking($entity);
        }

        return $this->context->entities[$key];
    }

    private function doCircularDependencyCheck(string $className, mixed $id, string $key): void
    {
        $identifyingName = $className . ':' . $id;

        if (array_key_exists($key, $this->context->initializing)) {
            throw new Exceptions\CircularDependencyFound(
                $this->context->initializing,
                $identifyingName
            );
        }

        $this->context->initializing[$key] = $identifyingName;
    }

    private function updateCircularDependencyCheck(string $key): void
    {
        unset($this->context->initializing[$key]);
    }

    /**
     * The return value is an object of type `$className`.
     *
     * Returns the entity for a relation. An entity that's in the identity map already is returned as with get().
     * Otherwise it's returned as a lazy ghost with only its id set, which is loaded on first access to any other
     * property - so a relation that is never followed costs no query, and reading its id doesn't either. The ghost
     * is registered in the identity map right away, so a later get() for the same id returns the same instance.
     *
     * Entities whose actual class has to be read from storage first (inheritance with a stored original class) can't
     * be lazy, and are loaded right away.
     */
    /*
     * This is specified in PhpStorm in .phpstorm.meta.php
     */
    public function reference(string $className, mixed $id): object
    {
        $key = $this->keyMaker->get($className, $id);

        if (array_key_exists($key, $this->context->entities)) {
            return $this->get($className, $id);
        }

        $entity = $this->initializer->initializeLazily(
            $className,
            $id,
            $this,
            function (object $entity): void {
                unset($this->context->unloadedEntities[$entity]);

                $this->startTracking($entity);
            });

        if ($entity === null) {
            return $this->get($className, $id);
        }

        $this->context->entities[$key] = $entity;
        $this->context->entityAccessTime[$key] = hrtime(true);
        $this->context->unloadedEntities[$entity] = true;

        ++$this->context->entityCount;

        if ($this->context->cachePurgeTriggerSize
                && $this->context->entityCount >= $this->context->cachePurgeTriggerSize) {
            $this->purge();
        }

        return $entity;
    }

    /**
     * Takes the current state of $entity as the baseline that flush() diffs against. An append-only entity is never
     * changed once stored, so it is left untracked instead: flush() then skips it, as for get(trackChanges: false).
     */
    private function startTracking(object $entity): void
    {
        if ($this->metaDataManager->get($entity::class)->appendOnly) {
            $this->context->untrackedEntities[$entity] = true;
        }
        else {
            $this->context->savedStates[$entity] = $this->snapshotManager->forEntity($entity);
        }
    }

    /**
     * The return value is an object of type `$className`.
     */
    /*
     * This is specified in PhpStorm in .phpstorm.meta.php
     */
    public function create(string $className, array $values = []): object
    {
        $entity = $this->initializer->initialize($className, $values);

        // A collection the values left out starts empty, also before a persist.
        $this->collectionInitializer->initialize($entity);

        if ($this->context->autoPersistOnCreate) {
            $this->persist($entity);

            if ($this->context->autoFlushOnCreate) {
                $this->flush();
            }
        }

        return $entity;
    }

    public function persist(object ...$entities): void
    {
        foreach ($entities as $entity) {
            if (!in_array($entity, $this->context->entities, true)) {
                $key = $entity::class . ':new:' . mt_rand();

                $this->uuidInitializer->initialize($entity);
                $this->collectionInitializer->initialize($entity);
                $this->referenceInitializer->initialize($entity, $this);

                $this->context->entities[$key] = $entity;
                $this->context->entityAccessTime[$key] = hrtime(true);

                ++$this->context->entityCount;

                dispatch(new DebugInformation('[entity-manager] persist %s', $entity::class));

                if ($this->context->cachePurgeTriggerSize
                        && $this->context->entityCount >= $this->context->cachePurgeTriggerSize) {
                    $this->purge();
                }
            }
        }
    }

    private function purge(): void
    {
        $this->flush();

        // Sort by access time (LRU)
        asort($this->context->entityAccessTime);

        $keysToRemove = array_slice(
            array_keys($this->context->entityAccessTime),
            0,
            $this->context->cachePurgeAmount,
            true
        );

        foreach ($keysToRemove as $key) {
            if (isset($this->context->entities[$key])) {
                $entity = $this->context->entities[$key];

                unset($this->context->entities[$key]);
                unset($this->context->entityAccessTime[$key]);
                unset($this->context->savedStates[$entity]);
                unset($this->context->untrackedEntities[$entity]);
            }
        }

        $this->context->entityCount = count($this->context->entities);
    }

    public function delete(object $entity): void
    {
        if (!in_array($entity, $this->context->entitiesToDelete, true)) {
            $this->context->entitiesToDelete[] = $entity;
        }
    }

    /**
     * Remove an entity from the identity map without marking it for deletion in the database.
     * Use this when an entity was created and persisted in memory, but the INSERT failed
     * (e.g. due to a race condition), so it should be dropped rather than retried on the next flush.
     */
    public function discard(object $entity): void
    {
        $key = array_search($entity, $this->context->entities, true);

        if ($key !== false) {
            unset($this->context->entities[$key]);
            unset($this->context->entityAccessTime[$key]);

            --$this->context->entityCount;
        }

        unset($this->context->savedStates[$entity]);
        unset($this->context->untrackedEntities[$entity]);
    }

    public function clear(): void
    {
        $this->context->entities = [];
        $this->context->entityCount = 0;
        $this->context->entitiesToDelete = [];
        $this->context->savedStates = new \WeakMap();
        $this->context->untrackedEntities = new \WeakMap();
        $this->context->unloadedEntities = new \WeakMap();
        $this->context->afterFlushCallbacks = [];

        dispatch(new Events\MustClearEntityValueCaches());
    }

    /**
     * Runs $callback once the current unit of work is stored: at the end of the outermost flush() in progress, or of
     * the next one when none is. Use it for work that belongs to this unit of work but reads what it changed from
     * storage, such as an event listener that queries entities its event's flush has not written yet. What the
     * callback changes is written by that same flush.
     *
     * A $key given twice before the callbacks run keeps the first callback only, so work asked for by several
     * changes runs once.
     */
    public function afterFlush(\Closure $callback, string|null $key = null): void
    {
        $key ??= 'callback:' . spl_object_id($callback);

        if (!array_key_exists($key, $this->context->afterFlushCallbacks)) {
            $this->context->afterFlushCallbacks[$key] = $callback;
        }
    }

    public function flush(): void
    {
        // A flush started inside another, by a listener, only writes: the after-flush callbacks wait for the
        // outermost one, so they run once everything is stored
        if ($this->context->flushing) {
            $this->writeChanges();

            return;
        }

        $this->context->flushing = true;

        try {
            $this->writeChanges();

            $round = 0;

            while ($callbacks = $this->takeAfterFlushCallbacks()) {
                if (++$round >= self::MAX_FLUSH_ROUNDS) {
                    throw new Exceptions\AfterFlushCallbacksDoNotSettle(
                        self::MAX_FLUSH_ROUNDS,
                        array_keys($callbacks)
                    );
                }

                foreach ($callbacks as $callback) {
                    $callback();
                }

                $this->writeChanges();
            }
        }

        finally{
            $this->context->flushing = false;

            // The callbacks of a unit of work that failed must not run on the next flush
            $this->context->afterFlushCallbacks = [];
        }
    }

    /** @return array<string, \Closure> */
    private function takeAfterFlushCallbacks(): array
    {
        $callbacks = $this->context->afterFlushCallbacks;
        $this->context->afterFlushCallbacks = [];

        return $callbacks;
    }

    private function writeChanges(): void
    {
        dispatch(new DebugInformation('[entity-manager] flushing'));

        dispatch(new DebugInformation(
            '[entity-manager] flush over %s tracked entities',
            count($this->context->entities)
        ));

        $changes = $this->changeFinder->gather(
            fn() => $this->trackableEntities(),
            fn() => $this->context->savedStates,
            fn() => $this->context->entitiesToDelete
        );

        $round = 0;

        do {
            $this->updateEntityStates($changes);

            // After-flush handlers (e.g. the change log, and the listeners of entity events) may create entities
            // and change existing ones. All of that is written within this same flush, so a listener never has to
            // flush itself just to get its changes stored. Nothing more can have changed when no handler did anything.
            if (!$this->flushManager->flush($changes)) {
                break;
            }

            if (++$round >= self::MAX_FLUSH_ROUNDS) {
                throw new Exceptions\FlushDoesNotSettle(self::MAX_FLUSH_ROUNDS, $changes);
            }

            $changes = $this->changeFinder->gather(
                fn() => $this->trackableEntities(),
                fn() => $this->context->savedStates,
                fn() => $this->context->entitiesToDelete
            );
        } while ($changes->hasChanges());

        dispatch(new Events\MustClearEntityValueCaches());
    }

    private function updateEntityStates(Snapshots\Changes $changes): void
    {
        foreach ($this->context->entities as $key => $entity) {
            if (in_array($entity, $this->context->entitiesToDelete, true)) {
                unset($this->context->entities[$key]);
                unset($this->context->savedStates[$entity]);
            }
            elseif ($snapshot = $changes->entitySnapshot($entity)) {
                // $changes already holds the Snapshot ChangeFinder built while diffing this
                // entity, so reuse it here instead of paying for a second full reflection pass
                // via snapshotManager->forEntity(). An entity with no snapshot on $changes had
                // no changes this round (or wasn't part of it), so its existing saved state is
                // still correct as-is and needs no work at all.
                if ($this->metaDataManager->get($entity::class)->appendOnly) {
                    // Only a new append-only entity is tracked, and after this insert it can't change any more, so
                    // later flushes have nothing to diff on it
                    $this->context->untrackedEntities[$entity] = true;
                }
                else {
                    $this->context->savedStates[$entity] = $snapshot;
                }
            }

            // Calling a method on a ghost that hasn't been loaded would load it, and it has nothing to reset anyway
            if ($entity instanceof TracksChanges && !isset($this->context->unloadedEntities[$entity])) {
                $entity->resetChangeTracking();
            }
        }

        $this->context->entitiesToDelete = [];
    }

    /**
     * $this->context->entities minus anything fetched with get(trackChanges: false) -- those are
     * cached in the identity map (so repeated get() calls don't re-hydrate) but must never reach
     * ChangeFinder::gather(): having no saved state would otherwise make gatherChanges() treat
     * them as brand new and re-insert them, rather than simply leaving them undiffed.
     *
     * Lazy ghosts that haven't been loaded are left out for the same reason: they have no saved
     * state either, they can't have changed (any write loads them first), and snapshotting them
     * would load them.
     *
     * @return object[]
     */
    private function trackableEntities(): array
    {
        return array_filter(
            $this->context->entities,
            fn(object $entity)
                => !isset($this->context->untrackedEntities[$entity])
                    && !isset($this->context->unloadedEntities[$entity
                ]
            )
        );
    }

    public function cacheSize(): int
    {
        return $this->context->entityCount;
    }

    #[EventListener]
    public function handleResetEntityKey(Events\ResetEntityKey $event): void
    {
        $this->resetKey($event->entity);
    }

    public function resetKey(object $entity): void
    {
        $id = $this->idValue->fromEntity($entity);
        $newKey = $this->keyMaker->get($entity::class, $id);

        // Strict: a loose comparison compares the properties of every entity of the same class, which loads lazy
        // ghosts and could match a different entity with equal values
        $oldKey = array_search($entity, $this->context->entities, true);
        $this->context->entities[$newKey] = $entity;

        unset($this->context->entities[$oldKey]);

        if (isset($this->context->entityAccessTime[$oldKey])) {
            $this->context->entityAccessTime[$newKey] = $this->context->entityAccessTime[$oldKey];

            unset($this->context->entityAccessTime[$oldKey]);
        }
    }

    #[EventListener]
    public function handleFindEntity(Events\FindEntity $event): void
    {
        $event->entity = $event->lazy
            ? $this->reference($event->type, $event->id)
            : $this->get($event->type, $event->id);
    }

    /** @noinspection PhpUnusedParameterInspection */
    #[EventListener]
    public function handleBeforeResponse(BeforeResponse $event): void
    {
        $this->flush();
    }
}
