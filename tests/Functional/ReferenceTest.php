<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\{EntityManager, MetaData\Compiler};
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\References\{ChildEntities, ChildEntity, ParentEntity};

class ReferenceTest extends BaseTestClass
{
    public function testCollectionInitialization(): void
    {
        $parent = service(EntityManager::class)->get(ParentEntity::class, 1);

        self::assertInstanceOf(ChildEntities::class, $parent->children);
        self::assertInstanceOf(ChildEntity::class, $parent->children[0]);
    }

    // A #[ReferencedBy] property is not stored, so it is a back-reference in the meta
    // data rather than a property; the referencing property is found by its type.
    public function testCollectionMetaData(): void
    {
        $metaData = service(Compiler::class)->compile(ParentEntity::class);
        $backReferences = array_column($metaData->backReferences, null, 'name');

        self::assertNull($metaData->property('children', ignoreUnknownProperties: true));
        self::assertArrayHasKey('children', $backReferences);
        self::assertSame(ChildEntity::class, $backReferences['children']->entity);
        self::assertSame('parentEntity', $backReferences['children']->property);
        self::assertSame(ChildEntities::class, $backReferences['children']->collectionClass);
    }
}
