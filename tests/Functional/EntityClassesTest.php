<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\EntityClasses;
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\MockEntity;

class EntityClassesTest extends BaseTestClass
{
    public function testFetch(): void
    {
        $classes = service(EntityClasses::class)->get();

        self::assertIsInt(array_search(MockEntity::class, $classes));
    }
}
