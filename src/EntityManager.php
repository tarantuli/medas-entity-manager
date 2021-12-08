<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Snapshots\SnapshotManager;
use Medas\EntityManager\Storage\UnitOfWork;
use Medas\EntityManager\Storage\UnitOfWorkExecutor;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManager
{
    private array $entities = [];
    private \SplObjectStorage $persistedStates;

    public function __construct(
        private EntityInitializer  $entityInitializer,
        private EntityPersister    $entityPersister,
        private IdHash             $idHash,
        private SnapshotManager    $snapshotManager,
        private UnitOfWorkExecutor $unitOfWorkExecutor,
    )
    {
        $this->clear();
    }

    public function clear(): void
    {
        $this->entities = [];
        $this->persistedStates = new \SplObjectStorage();
    }

    public function get(string $className, mixed $id): object
    {
        $key = $className . ':' . $this->idHash->get($className, $id);

        if (!array_key_exists($key, $this->entities)) {
            $entity = $this->entityInitializer->initialize($className, $id);
            $this->persistedStates[$entity] = $this->snapshotManager->forEntity($entity);
            $this->entities[$key] = $entity;
        }

        return $this->entities[$key];
    }

    public function flush(): void
    {
        $unitOfWork = new UnitOfWork();

        foreach ($this->entities as $entity) {
            $this->entityPersister->persist(
                $entity,
                $this->persistedStates[$entity],
                $unitOfWork
            );
        }

        if ($this->unitOfWorkExecutor->execute($unitOfWork)) {
            foreach ($this->entities as $entity) {
                $this->persistedStates[$entity] = $this->snapshotManager->forEntity($entity);
            }
        }
    }
}
