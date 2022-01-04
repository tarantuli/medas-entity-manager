<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Entities\Flusher;
use Medas\EntityManager\Entities\IdValues;
use Medas\EntityManager\Entities\Initializer;
use Medas\EntityManager\Entities\KeyMaker;
use Medas\EntityManager\Repositories\Repository;
use Medas\EntityManager\Snapshots\SnapshotManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManager
{
    private array $entities;
    private \SplObjectStorage $savedStates;

    public function __construct(
        private Flusher|null      $flusher,
        private IdValues          $idValues,
        private Initializer       $initializer,
        private KeyMaker          $keyMaker,
        private RepositoryManager $repositoryManager,
        private SnapshotManager   $snapshotManager,
    )
    {
        $this->clear();
    }

    public function clear(): void
    {
        $this->entities = [];
        $this->savedStates = new \SplObjectStorage();
    }

    public function repository(string $className): Repository
    {
        return $this->repositoryManager->forClass($className);
    }

    public function get(string $className, mixed $id): object
    {
        $id = $this->idValues->normalize($className, $id);
        $key = $this->keyMaker->get($className, $id);

        if (!array_key_exists($key, $this->entities)) {
            $entity = $this->initializer->initialize($className, $id);
            $this->savedStates[$entity] = $this->snapshotManager->forEntity($entity);
            $this->entities[$key] = $entity;
        }

        return $this->entities[$key];
    }

    public function flush(): void
    {
        if ($this->flusher->flush($this->entities, $this->savedStates)) {
            $this->updateEntityStates();
        }
    }

    private function updateEntityStates(): void
    {
        foreach ($this->entities as $entity) {
            $this->savedStates[$entity] = $this->snapshotManager->forEntity($entity);
        }
    }

    public function persist(object $entity): void
    {
        $key = $entity::class . ':new:' . mt_rand();
        $this->entities[$key] = $entity;
    }

    public function resetKey(object $entity): void
    {
        $id = $this->idValues->fromEntity($entity);
        $newKey = $this->keyMaker->get($entity::class, $id);
        $oldKey = array_search($entity, $this->entities);

        $this->entities[$newKey] = $entity;
        unset($this->entities[$oldKey]);
    }
}
