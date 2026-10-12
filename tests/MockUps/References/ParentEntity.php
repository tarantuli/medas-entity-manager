<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\References;

use Medas\EntityManager\Attributes\{Entity, Id, ReferencedBy};

#[Entity]
class ParentEntity
{
    #[Id]
    public int $id;

    #[ReferencedBy(ChildEntity::class)]
    public ChildEntities $children;

    public ChildEntity $mostImportantChild;
}
