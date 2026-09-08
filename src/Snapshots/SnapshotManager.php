<?php

declare(strict_types=1);

namespace Medas\EntityManager\Snapshots;

use Medas\Core\{
    AttributeChecker,
    Attributes\DataHolder,
    Attributes\PreferredDefault,
    Attributes\Service,
    Interfaces\HasId,
    Interfaces\Serializer,
    Interfaces\TracksChanges,
    Interfaces\Uuid
};
use Medas\EntityManager\MetaDataManager;

#[Service]
readonly class SnapshotManager
{
    public function __construct(
        private AttributeChecker $attributeChecker,
        private MetaDataManager  $metaDataManager,

        #[PreferredDefault('Medas\ObjectToArraySerializer\ObjectToArraySerializer')]
        private Serializer       $serializer,
    )
    {
    }

    /** @return PropertyChange[] */
    public function findPropertyChanges(object $entity, Snapshot|null $initial): array
    {
        $changes = [];
        $initialValues = $initial === null ? [] : $initial->data;

        foreach ($this->forEntity($entity)->data as $property => $current) {
            $initialValue = $initialValues[$property] ?? null;

            if ($this->valueHasChanged($current, $initialValue)) {
                $changes[$property] = new PropertyChange($initialValue, $current);
            }
        }

        return $changes;
    }

    private function valueHasChanged(mixed $current, mixed $initial): bool
    {
        if ($current instanceof TracksChanges) {
            return $current->hasChanged();
        }

        if ($current instanceof Uuid && $initial instanceof Uuid) {
            return $current->toBytes() !== $initial->toBytes();
        }

        if (is_object($current)
                && is_object($initial)
                && $current::class === $initial::class
                && $this->attributeChecker->hasAttribute($current::class, DataHolder::class)) {
            return $this->serializer->serialize($current) !== $this->serializer->serialize($initial);
        }

        if ($current instanceof HasId && $initial instanceof HasId && $current::class === $initial::class) {
            $currentId = $current->id();
            $initialId = $initial->id();

            if ($currentId instanceof Uuid && $initialId instanceof Uuid) {
                return $currentId->toBytes() !== $initialId->toBytes();
            }
            else {
                return $currentId !== $initialId;
            }
        }

        if ($current instanceof \DateTimeInterface && $initial instanceof \DateTimeInterface) {
            return $current->getTimestamp() !== $initial->getTimestamp();
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
