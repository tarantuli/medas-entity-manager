<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\Collections;

use Medas\Core\Collections\GenericCollection;
use Medas\EntityManager\Attributes\EntityCollection;
use Medas\EntityManagerTest\MockUps\MockEntity;

/**
 * @extends GenericCollection<MockEntity>
 */
#[EntityCollection(MockEntity::class)]
class MockEntityCollection extends GenericCollection
{
}
