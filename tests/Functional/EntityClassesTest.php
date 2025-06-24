<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\{EntityClasses, EntityClassFinder};
use Medas\EntityManagerTest\{BaseTestClass, MockUps\MockEntity};

class EntityClassesTest extends BaseTestClass
{
    public function testFetch(): void
    {
        $classes = service(EntityClasses::class)->get();

        self::assertIsInt(array_search(MockEntity::class, $classes));
    }

    public function testFindByStore(): void
    {
        $metaData = service(EntityClassFinder::class)->getByStore('mock_entities');

        self::assertEquals(MockEntity::class, $metaData->className);
    }
}
