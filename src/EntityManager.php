<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Entities\{IdValues, Initializer, KeyMaker};
use Medas\EntityManager\Snapshots\SnapshotManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManager
{
    protected array $entities;
    protected array $entitiesToDelete;
    protected \SplObjectStorage $savedStates;

    public function __construct(
        private readonly FlushManager    $flushManager,
        private readonly IdValues        $idValues,
        private readonly Initializer     $initializer,
        private readonly KeyMaker        $keyMaker,
        private readonly SnapshotManager $snapshotManager,
    )
    {
        $this->clear();
    }

    public function clear(): void
    {
        $this->entities = [];
        $this->entitiesToDelete = [];
        $this->savedStates = new \SplObjectStorage();
    }

    /**
     * The return value  is an object of type $className. This is specified in PhpStorm in .phpstorm.meta.php
     */
    public function get(string $className, mixed $id): object
    {
        $id = $this->idValues->normalize($className, $id);
        $key = $this->keyMaker->get($className, $id);

        if (!array_key_exists($key, $this->entities)) {
            $entity = $this->initializer->initializeAndHydrate($className, $id);
            $this->savedStates[$entity] = $this->snapshotManager->forEntity($entity);
            $this->entities[$key] = $entity;
        }

        return $this->entities[$key];
    }

    public function delete(object $entity): void
    {
        if (!in_array($entity, $this->entitiesToDelete, true)) {
            $this->entitiesToDelete[] = $entity;
        }
    }

    public function flush(): void
    {
        $this->flushManager->flush($this->entities, $this->savedStates, $this->entitiesToDelete);
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
        }

        $this->entitiesToDelete = [];
    }

    public function persist(object ...$entities): void
    {
        foreach ($entities as $entity) {
            if (!in_array($entity, $this->entities, true)) {
                $key = $entity::class . ':new:' . mt_rand();
                $this->entities[$key] = $entity;
            }
        }
    }

    public function resetKey(object $entity): void
    {
        $id = $this->idValues->fromEntity($entity);
        $newKey = $this->keyMaker->get($entity::class, $id);
        $oldKey = array_search($entity, $this->entities);

        $this->entities[$newKey] = $entity;
        unset($this->entities[$oldKey]);
    }

    /**
     * The return value  is an object of type $className. This is specified in PhpStorm in .phpstorm.meta.php
     */
    public function create(string $className, array $conditions): object
    {
        return $this->initializer->initialize($className, $conditions);
    }
}
