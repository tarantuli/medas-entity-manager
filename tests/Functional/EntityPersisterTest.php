<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\Test\BaseTest;
use Medas\Test\MockUps\StoredEntity;

class EntityPersisterTest extends BaseTest
{
    public function testPersist(): void
    {
        $entity = em()->get(StoredEntity::class, 1);
        $entity->name = $newName = (string) mt_rand();

        em()->flush();
        em()->clear();

        $entity = em()->get(StoredEntity::class, 1);

        self::assertEquals($newName, $entity->name);
    }
}
