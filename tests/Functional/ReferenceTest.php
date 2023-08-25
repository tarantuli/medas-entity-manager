<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\Hydration\Hydrator;
use Medas\EntityManager\MetaData\Compiler;
use Medas\EntityManager\Types\Collection;
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\References\{ChildEntities,
    ChildEntity,
    ParentEntity,
    TestEntityValueFetcher,
    TestSelectorRecordsFetcher};

class ReferenceTest extends BaseTestClass
{
    public function testCollectionInitialization(): void
    {
        service(Hydrator::class)->setEntityValueFetcher(medas()->objectInstantiator()->instantiate(TestEntityValueFetcher::class));
        service(Hydrator::class)->setSelectorRecordsFetcher(medas()->objectInstantiator()->instantiate(TestSelectorRecordsFetcher::class));
        $parent = em()->get(ParentEntity::class, 1);

        self::assertInstanceOf(ChildEntities::class, $parent->children);
        self::assertInstanceOf(ChildEntity::class, $parent->children[0]);

        service(Hydrator::class)->setEntityValueFetcher(null);
    }

    public function testCollectionMetaData(): void
    {
        $metaData = service(Compiler::class)->compile(ParentEntity::class);

        $childData = $metaData->property('children');

        $dataType = $childData->type;
        self::assertInstanceOf(Collection::class, $dataType);
        self::assertEquals(ChildEntity::class, $dataType->contentType);
    }
}
