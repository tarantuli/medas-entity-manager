<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity]
class MockEntityCompositeId
{
    #[Id]
    private int $id;

    #[Id]
    private string $name;

    public function id(): int
    {
        return $this->id;
    }
}
