<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Hydration\ValueGetter;
use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaDataManager;
use Medas\EntityManager\Snapshots\Snapshot;
use Medas\EntityManager\Snapshots\SnapshotManager;
use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\EntityManager\Storage\UnitOfWork\UnitOfWork;
use Medas\EntityManager\Storage\UnitOfWork\UnitOfWorkManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Persister
{
    public function __construct(
        private MetaDataManager   $metaDataManager,
        private SnapshotManager   $snapshotManager,
        private UnitOfWorkManager $unitOfWorkManager,
        private ValueGetter       $valueGetter,
    )
    {
    }

    public function prepare(object $entity, Snapshot|null $initialState, UnitOfWork $unitOfWork): void
    {
        if ($initialState === null) {
            $this->prepareCreate($entity, $unitOfWork);
            return;
        }

        $changedValues = $this->snapshotManager->getChanges($entity, $initialState);

        if ($changedValues === []) {
            return;
        }

        $this->prepareUpdate($entity, $changedValues, $unitOfWork);
    }

    private function prepareCreate(object $entity, UnitOfWork $unitOfWork): void
    {
        $metaData = $this->metaDataManager->get($entity::class);
        $serializedValues = [];

        foreach ($metaData->properties as $property) {
            if ($property->reflection->isInitialized($entity)) {
                $value = $property->reflection->getValue($entity);
                $serializedValues[$property->name] = $property->type->serialize($value);
            }
        }

        $onComplete = $this->generatedValueSetter($metaData, $entity);

        $this->unitOfWorkManager->queueCreate(
            $unitOfWork,
            $metaData->getTable(),
            $serializedValues,
            $onComplete
        );
    }

    private function generatedValueSetter(MetaData $metaData, object $entity): ?\Closure
    {
        if (!$metaData->idProperty?->isGeneratedValue) {
            return null;
        }

        return function (Database $database) use ($metaData, $entity) {
            $metaData->idProperty->reflection->setValue($entity, $database->lastInsertId());
            em()->resetKey($entity);
        };
    }

    private function prepareUpdate(object $entity, array $changedValues, UnitOfWork $unitOfWork): void
    {
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
