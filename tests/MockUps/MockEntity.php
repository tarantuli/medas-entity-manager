<?php

declare(strict_types=1);

namespace Medas\Test\MockUps;

use Medas\EntityManager\Attributes\{Entity, Id, IsUnique};
use Medas\EntityManager\Types as Type;

#[Entity]
class MockEntity
{
    #[Type\Text]
    #[IsUnique]
    public string $name;
    #[Id]
    #[Type\Integer]
    private int $id;

    public function id(): int
    {
        return $this->id;
    }
}
