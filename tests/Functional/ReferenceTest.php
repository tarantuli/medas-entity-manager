<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\Core\Types\Collection;
use Medas\EntityManager\{MetaData\Compiler};
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\References\{ChildEntities,ChildEntity,ParentEntity};

class ReferenceTest extends BaseTestClass
{
    public function testCollectionInitialization(): void
    {
        $parent = em()->get(ParentEntity::class, 1);

        self::assertInstanceOf(ChildEntities::class, $parent->children);
        self::assertInstanceOf(ChildEntity::class, $parent->children[0]);
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
