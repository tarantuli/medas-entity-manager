<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\Core\GlobalRepository;
use Medas\EntityManager\Hydration\Hydrator;
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\References\{ChildEntities, ChildEntity, ParentEntity, TestFetcher};

class ReferenceTest extends BaseTestClass
{
    public function testCollectionInitialization(): void
    {
        $flusher = GlobalRepository::objectInstantiator()->instantiate(TestFetcher::class);
        service(Hydrator::class)->setFetcher($flusher);
        $parent = em()->get(ParentEntity::class, 1);

        self::assertInstanceOf(ChildEntities::class, $parent->children);
        self::assertInstanceOf(ChildEntity::class, $parent->children[0]);

        service(Hydrator::class)->setFetcher(null);
    }
}
