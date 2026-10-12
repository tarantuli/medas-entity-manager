<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Lazy;

use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity]
class LazyAuthor
{
    #[Id]
    public int $id;

    public string $name;

    public function id(): int
    {
        return $this->id;
    }
}
