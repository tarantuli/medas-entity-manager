<?php

declare(strict_types=1);

namespace Medas\EntityManager\Snapshots;

use Medas\Core\{Attributes\Service, Interfaces\TracksChanges};
use Medas\EntityManager\MetaDataManager;

#[Service]
readonly class SnapshotManager
{
    public function __construct(
        private MetaDataManager $metaDataManager,
    )
    {
    }

    public function findChanges(object $entity, Snapshot|null $initial): array
    {
        return array_udiff_assoc(
            $this->forEntity($entity)->data,
            $initial === null ? [] : $initial->data,
            $this->compareValues(...)
        );
    }

    private function compareValues(mixed $current, mixed $initial): int
    {
        if ($current instanceof TracksChanges) {
            return (int) $current->hasChanged();
        }

        return (int) ($current !== $initial);
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
