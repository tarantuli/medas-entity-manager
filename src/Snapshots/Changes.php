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

    public function addCreate(object $entity, array $values): void
    {
        $this->creates[] = $entity;
        $this->createValues[spl_object_id($entity)] = $values;
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
