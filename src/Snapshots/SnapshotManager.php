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

    /** @return PropertyChange[] */
    public function findPropertyChanges(object $entity, Snapshot|null $initial): array
    {
        $changes = [];
        $initialValues = $initial === null ? [] : $initial->data;

        foreach ($this->forEntity($entity)->data as $property => $current) {
            if ($this->valueHasChanged($current, $initialValues[$property])) {
                $changes[] = new PropertyChange($initialValues[$property], $current);
            }
        }

        return $changes;
    }

    private function valueHasChanged(mixed $current, mixed $initial): bool
    {
        if ($current instanceof TracksChanges) {
            return $current->hasChanged();
        }

        return $current !== $initial;
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
