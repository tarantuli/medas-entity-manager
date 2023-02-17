<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\Entities\ReferenceCollection;
use Medas\EntityManager\Hydration\Hydrator;
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\References\{ChildEntity, ParentEntity, TestFetcher};

class ReferenceTest extends BaseTestClass
{
    public function testCollectionInitialization(): void
    {
        service(Hydrator::class)->setFetcher(sm()->instantiate(TestFetcher::class));
        $parent = em()->get(ParentEntity::class, 1);

        self::assertInstanceOf(ReferenceCollection::class, $parent->children);
        self::assertInstanceOf(ChildEntity::class, $parent->children[0]);

        service(Hydrator::class)->setFetcher(null);
    }
}
