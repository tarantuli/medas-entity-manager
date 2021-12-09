<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Snapshots\SnapshotManager;
use Medas\EntityManager\Storage\UnitOfWork\UnitOfWork;
use Medas\EntityManager\Storage\UnitOfWork\UnitOfWorkExecutor;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManager
{
    private array $entities;
    private \SplObjectStorage $savedStates;

    public function __construct(
        private EntityInitializer  $entityInitializer,
        private EntityPersister    $entityPersister,
        private IdHash             $idHash,
        private IdValues           $idValues,
        private RepositoryManager  $repositoryManager,
        private SnapshotManager    $snapshotManager,
        private UnitOfWorkExecutor $unitOfWorkExecutor,
    )
    {
        $this->clear();
    }

    public function clear(): void
    {
        $this->entities = [];
        $this->savedStates = new \SplObjectStorage();
    }

    public function getRepository(string $className): Repository
    {
        return $this->repositoryManager->forClass($className);
    }

    public function get(string $className, mixed $id): object
    {
        $id = $this->idValues->normalize($className, $id);
        $key = $className . ':' . $this->idHash->get($id);

        if (!array_key_exists($key, $this->entities)) {
            $entity = $this->entityInitializer->initialize($className, $id);
            $this->savedStates[$entity] = $this->snapshotManager->forEntity($entity);
            $this->entities[$key] = $entity;
        }

        return $this->entities[$key];
    }

    public function flush(): void
    {
        $unitOfWork = new UnitOfWork();

        foreach ($this->entities as $entity) {
            $this->entityPersister->prepare(
                $entity,
                $this->savedStates[$entity] ?? null,
                $unitOfWork
            );
        }

        if ($this->unitOfWorkExecutor->execute($unitOfWork)) {
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
}
