<?php

declare(strict_types=1);

namespace Medas\Test\MockUps\References;

use Medas\Core\Collection;
use Medas\EntityManager\Attributes\{Entity, Id, References};

#[Entity]
class ParentEntity
{
    #[Id]
    public int $id;

    #[References(ChildEntity::class)]
    public Collection $children;
}
