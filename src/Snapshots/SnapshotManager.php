<?php

declare(strict_types=1);

namespace Medas\EntityManager\Snapshots;

use Medas\EntityManager\MetaDataManager;
use Medas\ServiceManager\Service;

#[Service]
class SnapshotManager
{
    public function __construct(
        private readonly MetaDataManager $metaDataManager,
    )
    {
    }

    public function findChanges(object $entity, Snapshot|null $initial): array
    {
        return array_udiff_assoc(
            $this->forEntity($entity)->data,
            $initial === null ? [] : $initial->data,
            fn($a, $b) => (int) ($a !== $b)
        );
    }

    public function forEntity(object $entity): Snapshot
    {
        $snapshot = new Snapshot();
        $metaData = $this->metaDataManager->get($entity::class);

        foreach ($metaData->properties as $property) {
            $snapshot->data[$property->name] = $property->reflection->isInitialized($entity)
                ? $property->reflection->getValue($entity)
                : null;
        }

        return $snapshot;
    }
}
