<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\Test\BaseTest;
use Medas\Test\MockUps\StoredEntity;

class HydratorTest extends BaseTest
{
    public function testHydrateEntity(): void
    {
        $entityManager = $this->getEntityManager();

        $entity = $entityManager->get(StoredEntity::class, 1);

        $this->assertInstanceOf(StoredEntity::class, $entity);
        $this->assertEquals(1, $entity->id());
        $this->assertEquals('First entity', $entity->name);
        $this->assertNull($entity->createdAt);
    }
}
