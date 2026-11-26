<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Inheritance;

use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity(store: 'mock_unrelated_entities')]
class UnrelatedEntity
{
    #[Id]
    public int $id;
}
