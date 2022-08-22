<?php

declare(strict_types=1);

namespace Medas\Test\MockUps\References;

use Medas\EntityManager\Attributes\{Entity, Id, References};
use Medas\Core\Collections\Collection;

#[Entity]
class ParentEntity
{
    #[Id]
    public int $id;

    #[References(ChildEntity::class)]
    public Collection $children;
}
