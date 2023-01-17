<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\EntityManager\Attributes\{Entity, Id, IsUnique, Property};

#[Entity]
class MockEntity
{
    #[Id]
    private int $id;

    #[Property]
    #[IsUnique]
    public string $name;

    public function id(): int
    {
        return $this->id;
    }
}
