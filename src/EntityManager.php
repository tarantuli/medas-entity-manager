<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{Attributes\Service, Events\DebugInformation, Interfaces\TracksChanges};

#[Service]
class EntityManager
{
    protected array $entities;
    protected array $initializing;
    protected int $entityCount;
    protected array $entitiesToDelete;
    protected \SplObjectStorage $savedStates;
    private bool $autoPersistOnCreate = false;
    private bool $autoFlushOnCreate = false;
    private int|null $cachePurgeTriggerSize = null;
    private int|null $cachePurgeAmount = null;

    public function __construct(
        private readonly FlushManager              $flushManager,
        private readonly Entities\UuidSetter       $uuidSetter,
        private readonly Entities\IdValue          $idValue,
        private readonly Entities\Initializer      $initializer,
        private readonly Entities\KeyMaker         $keyMaker,
        private readonly Snapshots\SnapshotManager $snapshotManager,
        private readonly Snapshots\ChangeFinder    $changeFinder,
    )
    {
        $this->entities = [];
        $this->initializing = [];
        $this->entityCount = 0;
        $this->entitiesToDelete = [];
        $this->savedStates = new \SplObjectStorage();
    }

    public function autoPersistOnCreate(bool $value = true, bool $alsoFlush = true): void
    {
        $this->autoPersistOnCreate = $value;

        if ($value) {
            $this->autoFlushOnCreate($alsoFlush);
        }
    }

    public function autoFlushOnCreate(bool $value): void
    {
        $this->autoFlushOnCreate = $value;
    }

    public function setPurgingParameters(int|null $triggerSize, int|null $purgeAmount = null): void
    {
        $this->cachePurgeTriggerSize = $triggerSize;
        $this->cachePurgeAmount = $purgeAmount ?: (int) floor($triggerSize / 4);

        if ($this->cachePurgeAmount >= $this->cachePurgeTriggerSize) {
            throw new Exceptions\PurgeAmountShouldBeLessThanTriggerSize(
                $this->cachePurgeAmount,
                $this->cachePurgeTriggerSize
            );
        }
    }

    public function purge(): void
    {
        $this->flush();

        $toClear = array_slice($this->entities, 0, $this->cachePurgeAmount, true);

        foreach ($toClear as $index => $entity) {
            unset($this->entities[$index]);

            $this->savedStates->offsetUnset($entity);
        }

        $this->entityCount = count($this->entities);
    }

    public function clear(): void
    {
        $this->entities = [];
        $this->entityCount = 0;
        $this->entitiesToDelete = [];
        $this->savedStates = new \SplObjectStorage();

        dispatch(new Events\MustClearEntityValueCaches());
    }

    public function delete(object $entity): void
    {
        if (!in_array($entity, $this->entitiesToDelete, true)) {
            $this->entitiesToDelete[] = $entity;
        }
    }

    public function flush(): void
    {
        dispatch(new DebugInformation('[entity-manager] flushing'));

        $changes = $this->changeFinder->gather(
            fn() => $this->entities,
            fn() => $this->savedStates,
            fn() => $this->entitiesToDelete
        );

        $this->updateEntityStates();
        $this->flushManager->flush($changes);

        dispatch(new Events\MustClearEntityValueCaches());
    }

    public function updateEntityStates(): void
    {
        foreach ($this->entities as $key => $entity) {
            if (in_array($entity, $this->entitiesToDelete, true)) {
                unset($this->entities[$key]);
                unset($this->savedStates[$entity]);
            }
            else {
                $this->savedStates[$entity] = $this->snapshotManager->forEntity($entity);
            }

            if ($entity instanceof TracksChanges) {
                $entity->resetChangeTracking();
            }
        }

        $this->entitiesToDelete = [];
    }

    public function cacheSize(): int
    {
        return $this->entityCount;
    }

    public function persist(object ...$entities): void
    {
        foreach ($entities as $entity) {
            if (!in_array($entity, $this->entities, true)) {
                $key = $entity::class . ':new:' . mt_rand();

                $this->uuidSetter->processEntity($entity);

                $this->entities[$key] = $entity;

                ++$this->entityCount;

                if ($this->cachePurgeTriggerSize && $this->entityCount >= $this->cachePurgeTriggerSize) {
                    $this->purge();
                }
            }
        }
    }

    public function resetKey(object $entity): void
    {
        $id = $this->idValue->fromEntity($entity);
        $newKey = $this->keyMaker->get($entity::class, $id);
        $oldKey = array_search($entity, $this->entities);
        $this->entities[$newKey] = $entity;

        unset($this->entities[$oldKey]);
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

        if (!array_key_exists($key, $this->entities)) {
            $this->doCircularDependencyCheck($className, $id, $key);

            $entity = $this->initializer->initializeAndHydrate($className, $id);

            $this->updateCircularDependencyCheck($key);

            $this->savedStates[$entity] = $this->snapshotManager->forEntity($entity);
            $this->entities[$key] = $entity;

            ++$this->entityCount;

            if ($this->cachePurgeTriggerSize && $this->entityCount >= $this->cachePurgeTriggerSize) {
                $this->purge();
            }
        }

        return $this->entities[$key];
    }

    private function doCircularDependencyCheck(string $className, mixed $id, string $key): void
    {
        $identifyingName = $className . ':' . $id;

        if (array_key_exists($key, $this->initializing)) {
            throw new Exceptions\CircularDependencyFound($this->initializing, $identifyingName);
        }

        $this->initializing[$key] = $identifyingName;
    }

    private function updateCircularDependencyCheck(string $key): void
    {
        unset($this->initializing[$key]);
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

        if ($this->autoPersistOnCreate) {
            $this->persist($entity);

            if ($this->autoFlushOnCreate) {
                $this->flush();
            }
        }

        return $entity;
    }
}
