<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Inheritance;

use Medas\EntityManager\Attributes\{Entity, Id, StoreOriginalEntityType};

#[Entity(store: 'mock_rules'), StoreOriginalEntityType]
abstract class AbstractRule
{
    #[Id]
    public int $id;
}
