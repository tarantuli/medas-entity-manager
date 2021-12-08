<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Snapshots\Snapshot;
use Medas\EntityManager\Snapshots\SnapshotManager;
use Medas\EntityManager\Storage\UnitOfWork;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManager
{
    private array $entities = [];
    /** @var Snapshot[] */
    private array $initialStates = [];

    public function __construct(
        private EntityInitializer $entityInitializer,
        private IdHash            $idHash,
        private SnapshotManager   $snapshotManager,
    )
    {
    }

    public function get(string $className, mixed $id): object
    {
        $idHash = $this->idHash->get($className, $id);

        if (!array_key_exists($className, $this->entities)) {
            $this->entities[$className] = [];
        }

        if (!array_key_exists($idHash, $this->entities[$className])) {
            $entity = $this->entityInitializer->initializeEntity($className, $id);
            $this->initialStates[spl_object_id($entity)] = $this->snapshotManager->forEntity($entity);
            $this->entities[$className][$idHash] = $entity;
        }

        return $this->entities[$className][$idHash];
    }

    public function flush(): void
    {
        $unitOfWork = new UnitOfWork();
        foreach ($this->entities as $entity) {
            $diff = $this->snapshotManager->getDiff($entity, $this->initialStates[spl_object_id($entity)]);

            if ($diff === []) {
                continue;
            }

            $unitOfWork->updateRecord();
        }
    }
}
