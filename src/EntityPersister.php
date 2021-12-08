<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Hydration\ValueGetter;
use Medas\EntityManager\Snapshots\Snapshot;
use Medas\EntityManager\Snapshots\SnapshotManager;
use Medas\EntityManager\Storage\UnitOfWork\UnitOfWork;
use Medas\EntityManager\Storage\UnitOfWork\UnitOfWorkManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityPersister
{
    public function __construct(
        private MetaDataManager   $metaDataManager,
        private SnapshotManager   $snapshotManager,
        private UnitOfWorkManager $unitOfWorkManager,
        private ValueGetter       $valueGetter,
    )
    {
    }

    public function persist(object $entity, Snapshot|null $initialState, UnitOfWork $unitOfWork): void
    {
        $changedValues = $this->snapshotManager->getDiff($entity, $initialState);

        if ($changedValues === []) {
            return;
        }

        $metaData = $this->metaDataManager->get($entity::class);
        $serializedValues = [];

        foreach ($changedValues as $name => $value) {
            $property = $metaData->getProperty($name);
            $serializedValues[$name] = $property->type->serialize($value);
        }

        $this->unitOfWorkManager->queueUpdate(
            $unitOfWork,
            $metaData->getTable(),
            $serializedValues,
            $this->valueGetter->getValues($entity, $metaData->idProperties)
        );
    }
}
