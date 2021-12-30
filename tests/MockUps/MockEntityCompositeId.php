<?php

declare(strict_types=1);

namespace Medas\Test\MockUps;

use Medas\EntityManager\Attributes\{Entity, Id};
use Medas\EntityManager\Types as Type;

#[Entity]
class MockEntityCompositeId
{
    #[Id]
    #[Type\Integer]
    private int $id;

    #[Id]
    #[Type\Text]
    private string $name;

    public function id(): int
    {
        return $this->id;
    }
}
