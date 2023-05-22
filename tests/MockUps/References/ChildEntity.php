<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\References;

use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity]
class ChildEntity
{
    #[Id]
    public int $id;

    public ParentEntity $parentEntity;
}
