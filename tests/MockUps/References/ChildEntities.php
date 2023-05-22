<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\References;

use Medas\Core\Collections\LazyGenericCollection;
use Medas\EntityManager\Attributes\EntityCollection;

/**
 * @extends LazyGenericCollection<ChildEntity>
 */
#[EntityCollection(ChildEntity::class)]
class ChildEntities extends LazyGenericCollection
{
}
