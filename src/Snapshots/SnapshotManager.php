<?php

declare(strict_types=1);

namespace Medas\EntityManager\Snapshots;

use Medas\EntityManager\MetaDataManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class SnapshotManager
{
    public function __construct(
        private MetaDataManager $metaDataManager,
    )
    {
    }

    public function makeDiff(object $entity, Snapshot $initial): Snapshot
    {
        $current = $this->forEntity($entity);
        $current->data = array_diff($current->data, $initial->data);

        return $current;
    }

    public function forEntity(object $entity): Snapshot
    {
        $snapshot = new Snapshot();
        $metaData = $this->metaDataManager->get($entity::class);

        foreach ($metaData->properties as $property) {
            $snapshot->data[$property->name] = $property->reflection->getValue($entity);
        }

        return $snapshot;
    }
}
