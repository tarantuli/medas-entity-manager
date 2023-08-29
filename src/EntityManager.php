<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\TracksChanges;
use Medas\EntityManager\Entities\{IdValue, Initializer, KeyMaker};
use Medas\EntityManager\Events\MustClearEntityValueCaches;
use Medas\EntityManager\Snapshots\SnapshotManager;
use Medas\Events\Interfaces\EventDispatcher;

#[Service]
class EntityManager
{
    protected array $entities;
    protected int $entityCount;
    protected array $entitiesToDelete;
    protected \SplObjectStorage $savedStates;

    private bool $autoPersistOnCreate = false;
    private bool $autoFlushOnCreate = false;

    public function __construct(
        private readonly FlushManager    $flushManager,
        private readonly IdValue         $idValue,
        private readonly Initializer     $initializer,
        private readonly KeyMaker        $keyMaker,
        private readonly SnapshotManager $snapshotManager,
        private readonly EventDispatcher $eventDispatcher,
    )
    {
        $this->clear();
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

    public function clear(): void
    {
        $this->entities = [];
        $this->entityCount = 0;
        $this->entitiesToDelete = [];
        $this->savedStates = new \SplObjectStorage();
        $this->eventDispatcher->dispatch(new MustClearEntityValueCaches());
    }

    public function delete(object $entity): void
    {
        if (!in_array($entity, $this->entitiesToDelete, true)) {
            $this->entitiesToDelete[] = $entity;
        }
    }

    public function flush(): void
    {
        $this->flushManager->flush(fn() => $this->entities, fn() => $this->savedStates, fn() => $this->entitiesToDelete);
        $this->updateEntityStates();
    }

    private function updateEntityStates(): void
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

                $this->entities[$key] = $entity;
                ++$this->entityCount;
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
     * The return value is an object of type $className. This is specified in PhpStorm in .phpstorm.meta.php
     */
    public function get(string $className, mixed $id): object
    {
        $key = $this->keyMaker->get($className, $id);

        if (!array_key_exists($key, $this->entities)) {
            $entity = $this->initializer->initializeAndHydrate($className, $id);
            $this->savedStates[$entity] = $this->snapshotManager->forEntity($entity);
            $this->entities[$key] = $entity;
            ++$this->entityCount;
        }

        return $this->entities[$key];
    }

    /**
     * The return value is an object of type $className. This is specified in PhpStorm in .phpstorm.meta.php
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
