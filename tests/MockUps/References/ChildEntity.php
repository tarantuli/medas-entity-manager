<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\References;

use Medas\EntityManager\Attributes\{Entity, Id, Property};

#[Entity]
class ChildEntity
{
    #[Id]
    public int $id;

    #[Property]
    public ParentEntity $parentEntity;
}
