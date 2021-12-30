<?php

declare(strict_types=1);

namespace Medas\Test\MockUps;

use Medas\EntityManager\Attributes\{Entity, Id, IsUnique};
use Medas\EntityManager\Types as Type;

#[Entity]
class MockEntity
{
    #[Id]
    #[Type\Integer]
    private int $id;

    #[Type\Text]
    #[IsUnique]
    private string $name;

    public function id(): int
    {
        return $this->id;
    }
}
