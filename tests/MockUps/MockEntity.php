<?php

declare(strict_types=1);

namespace Medas\Test\MockUps;

use Medas\EntityManager\Attributes\Entity;
use Medas\EntityManager\Attributes\Id;
use Medas\EntityManager\Attributes\Types as Type;

#[Entity]
class MockEntity
{
    #[Id]
    #[Type\Integer]
    private int $id;

    #[Type\Text]
    private string $name;

    public function getId(): int
    {
        return $this->id;
    }
}
