<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\References;

use Medas\EntityManager\Attributes\{Entity, Id, References};

#[Entity]
class ParentEntity
{
    #[Id]
    public int $id;

    #[References(ChildEntity::class)]
    public ChildEntities $children;

    public ChildEntity $mostImportantChild;
}
