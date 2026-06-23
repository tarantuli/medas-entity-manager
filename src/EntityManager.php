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
    public function __construct(
        private Entities\IdValue          $idValue,
        private Entities\Initializer      $initializer,
        private Entities\KeyMaker         $keyMaker,
        private Entities\UuidSetter       $uuidSetter,
        private FlushManager              $flushManager,
        private Snapshots\ChangeFinder    $changeFinder,
        private Snapshots\SnapshotManager $snapshotManager,
        private EntityManagerContext      $context = new EntityManagerContext(),
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

    public function purge(): void
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

                $this->context->savedStates->offsetUnset($entity);
            }
        }

        $this->context->entityCount = count($this->context->entities);
    }

    public function clear(): void
    {
        $this->context->entities = [];
        $this->context->entityCount = 0;
        $this->context->entitiesToDelete = [];
        $this->context->savedStates = new \SplObjectStorage();

        dispatch(new Events\MustClearEntityValueCaches());
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
     * (e.g., due to a race condition), so it should be dropped rather than retried on the next flush.
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
    }

    public function flush(): void
    {
        dispatch(new DebugInformation('[entity-manager] flushing'));

        $changes = $this->changeFinder->gather(
            fn() => $this->context->entities,
            fn() => $this->context->savedStates,
            fn() => $this->context->entitiesToDelete
        );

        $this->updateEntityStates();
        $this->flushManager->flush($changes);

        dispatch(new Events\MustClearEntityValueCaches());
    }

    public function updateEntityStates(): void
    {
        foreach ($this->context->entities as $key => $entity) {
            if (in_array($entity, $this->context->entitiesToDelete, true)) {
                unset($this->context->entities[$key]);
                unset($this->context->savedStates[$entity]);
            }
            else {
                $this->context->savedStates[$entity] = $this->snapshotManager->forEntity($entity);
            }

            if ($entity instanceof TracksChanges) {
                $entity->resetChangeTracking();
            }
        }

        $this->context->entitiesToDelete = [];
    }

    public function cacheSize(): int
    {
        return $this->context->entityCount;
    }

    public function persist(object ...$entities): void
    {
        foreach ($entities as $entity) {
            if (!in_array($entity, $this->context->entities, true)) {
                $key = $entity::class . ':new:' . mt_rand();

                $this->uuidSetter->processEntity($entity);

                $this->context->entities[$key] = $entity;

                ++$this->context->entityCount;

                if ($this->context->cachePurgeTriggerSize
                        && $this->context->entityCount >= $this->context->cachePurgeTriggerSize) {
                    $this->purge();
                }
            }
        }
    }

    public function resetKey(object $entity): void
    {
        $id = $this->idValue->fromEntity($entity);
        $newKey = $this->keyMaker->get($entity::class, $id);
        $oldKey = array_search($entity, $this->context->entities);
        $this->context->entities[$newKey] = $entity;

        unset($this->context->entities[$oldKey]);
    }

    #[EventListener]
    public function handleResetEntityKey(Events\ResetEntityKey $event): void
    {
        $this->resetKey($event->entity);
    }

    /**
     * The return value is an object of type `$className`.
     */
    /*
     * This is specified in PhpStorm in .phpstorm.meta.php
     */
    public function get(string $className, mixed $id): object
    {
        $key = $this->keyMaker->get($className, $id);
        $this->context->entityAccessTime[$key] = hrtime(true);

        if (!array_key_exists($key, $this->context->entities)) {
            $this->doCircularDependencyCheck($className, $id, $key);

            try {
                $entity = $this->initializer->initializeAndHydrate($className, $id, $this);

                $this->updateCircularDependencyCheck($key);

                $this->context->savedStates[$entity] = $this->snapshotManager->forEntity($entity);
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

    #[EventListener]
    public function handleFindEntity(Events\FindEntity $event): void
    {
        $event->entity = $this->get($event->type, $event->id);
    }

    /** @noinspection PhpUnusedParameterInspection */
    #[EventListener]
    public function handleBeforeResponse(BeforeResponse $event): void
    {
        $this->flush();
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

        if ($this->context->autoPersistOnCreate) {
            $this->persist($entity);

            if ($this->context->autoFlushOnCreate) {
                $this->flush();
            }
        }

        return $entity;
    }
}
