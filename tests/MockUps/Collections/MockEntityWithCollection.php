<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Collections;

use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity('mock_entities_with_collection')]
class MockEntityWithCollection
{
    #[Id]
    private int $id;

    public string $name;
    public MockEntityCollection $members;

    public function id(): int
    {
        return $this->id;
    }
}
