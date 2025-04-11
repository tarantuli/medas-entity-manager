<?php

declare(strict_types=1);

namespace Medas\EntityManager\Snapshots;

class Changes
{
    /** @var object[] */
    private array $creates = [];

    /** @var object[] */
    private array $updates = [];

    /** @var PropertyChange[][] */
    private array $diffs = [];

    /** @var object[] */
    private array $deletes = [];

    public function addCreate(object $entity): void
    {
        $this->creates[] = $entity;
    }

    public function createdEntities(): array
    {
        return $this->creates;
    }

    /** @param PropertyChange[] $changes */
    public function addUpdate(object $entity, array $changes): void
    {
        $this->updates[] = $entity;
        $this->diffs[spl_object_id($entity)] = $changes;
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

    public function addDelete(object $entity): void
    {
        $this->deletes[] = $entity;
    }

    public function deletedEntities(): array
    {
        return $this->deletes;
    }
}
