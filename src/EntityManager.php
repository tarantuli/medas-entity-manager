<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Snapshots\Snapshot;
use Medas\EntityManager\Snapshots\SnapshotManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManager
{
    private array $entities = [];
    /** @var Snapshot[] */
    private array $initialStates = [];

    public function __construct(
        private EntityInitializer     $entityInitializer,
        private IdHash                $idHash,
        private MetaDataManager       $metadataManager,
        private SnapshotManager       $snapshotManager,
    )
    {
    }

    public function get(string $className, mixed $id): object
    {
        $metaData = $this->metadataManager->get($className);
        $idHash = $this->idHash->get($id, $metaData);

        if (!array_key_exists($className, $this->entities)) {
            $this->entities[$className] = [];
        }

        if (!array_key_exists($idHash, $this->entities[$className])) {
            $entity = $this->entityInitializer->initializeEntity($className, $metaData, $id);
            $this->initialStates[spl_object_id($entity)] = $this->snapshotManager->forEntity($entity);
            $this->entities[$className][$idHash] = $entity;
        }

        return $this->entities[$className][$idHash];
    }
}
