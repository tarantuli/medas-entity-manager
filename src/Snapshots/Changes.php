<?php

declare(strict_types=1);

namespace Medas\EntityManager\Snapshots;

class Changes
{
    /** @var object[] */
    private array $creates = [];

    /** @var PropertyChange[][] */
    private array $createValues = [];

    /** @var object[] */
    private array $updates = [];

    /** @var PropertyChange[][] */
    private array $diffs = [];

    /** @var object[] */
    private array $deletes = [];

    /** @var Snapshot[] keyed by spl_object_id() -- the fresh Snapshot taken while diffing a create/update */
    private array $snapshots = [];

    public function addCreate(object $entity, array $values, Snapshot $snapshot): void
    {
        $this->creates[] = $entity;
        $this->createValues[spl_object_id($entity)] = $values;
        $this->snapshots[spl_object_id($entity)] = $snapshot;
    }

    public function createdEntities(): array
    {
        return $this->creates;
    }

    /** @return PropertyChange[] */
    public function createValues(object $entity): array
    {
        return $this->createValues[spl_object_id($entity)] ?? [];
    }

    /** @param PropertyChange[] $changes */
    public function addUpdate(object $entity, array $changes, Snapshot $snapshot): void
    {
        $this->updates[] = $entity;
        $this->diffs[spl_object_id($entity)] = $changes;
        $this->snapshots[spl_object_id($entity)] = $snapshot;
    }

    public function updatedEntities(): array
    {
        return $this->updates;
    }

    /** @return PropertyChange[] */
    public function entityChanges(object $entity): array
    {
        return $this->diffs[spl_object_id($entity)] ?? [];
    }

    /**
     * The Snapshot taken of $entity while it was being diffed into this Changes, if it was seen
     * as a create or an update. Null for an entity that had no changes this round (its existing
     * saved state is therefore still correct and needs no update) or wasn't part of this gather.
     */
    public function entitySnapshot(object $entity): Snapshot|null
    {
        return $this->snapshots[spl_object_id($entity)] ?? null;
    }

    public function addDelete(object $entity): void
    {
        $this->deletes[] = $entity;
    }

    public function deletedEntities(): array
    {
        return $this->deletes;
    }

    public function hasChanges(): bool
    {
        return $this->creates || $this->updates || $this->deletes;
    }
}
