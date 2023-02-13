<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

class Changes
{
    private array $creates = [];
    private array $updates = [];
    private array $deletes = [];

    public function addCreate(object $entity): void
    {
        $this->creates[] = $entity;
    }

    public function creates(): array
    {
        return $this->creates;
    }

    public function addUpdate(object $entity, array $changes): void
    {
        $this->updates[] = [$entity, $changes];
    }

    public function updates(): array
    {
        return $this->updates;
    }

    public function addDelete(object $entity): void
    {
        $this->deletes[] = $entity;
    }

    public function deletes(): array
    {
        return $this->deletes;
    }
}
