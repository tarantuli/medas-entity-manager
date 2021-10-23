<?php

declare(strict_types=1);

namespace Medas\Test\MockUps;

use Medas\EntityManager\Attributes\Entity;
use Medas\EntityManager\Attributes\Id;
use Medas\EntityManager\Attributes\Types as Type;

#[Entity]
class MockEntityCompositeId
{
    #[Id]
    #[Type\Integer]
    private int $id;

    #[Id]
    #[Type\Text]
    private string $name;

    public function getId(): int
    {
        return $this->id;
    }
}
