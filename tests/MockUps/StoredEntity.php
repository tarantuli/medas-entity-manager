<?php

declare(strict_types=1);

namespace Medas\Test\MockUps;

use Medas\EntityManager\Attributes\{Entity, Id, IsNullable, IsUnique, Types as Type};

#[Entity(table: 'stored_entities')]
class StoredEntity
{
    #[Id]
    #[Type\Integer]
    private int $id;

    #[Type\Text]
    #[IsUnique]
    public string $name;

    #[Type\DateTime]
    #[IsNullable]
    public ?\DateTime $createdAt;

    public function id(): int
    {
        return $this->id;
    }
}
