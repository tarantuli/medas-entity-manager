<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional\MetaData;

use Medas\EntityManager\MetaData\Compiler;
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\{
    Collections\MockEntityCollection,
    Collections\MockEntityWithCollection,
    MockEntity
};

// A property's #[EntityCollection] class and content type are worked out when the
// metadata is compiled, survive the metadata cache, and are worked out again for
// metadata cached before they existed.
class CollectionPropertyTest extends BaseTestClass
{
    public function testACollectionPropertyKnowsItsCollectionClassAndContentType(): void
    {
        $metaData = service(Compiler::class)->compile(MockEntityWithCollection::class);

        self::assertSame(
            MockEntityCollection::class,
            $metaData->property('members')->collectionClass
        );

        self::assertSame(MockEntity::class, $metaData->property('members')->collectionContentType);
        self::assertNull($metaData->property('name')->collectionClass);
        self::assertNull($metaData->property('name')->collectionContentType);
    }

    public function testTheCollectionSurvivesTheCache(): void
    {
        $property = service(Compiler::class)->compile(MockEntityWithCollection::class)->property('members');
        $restored = unserialize(serialize($property));

        self::assertSame(MockEntityCollection::class, $restored->collectionClass);
        self::assertSame(MockEntity::class, $restored->collectionContentType);
    }

    public function testMetadataCachedBeforeTheFieldsExistedStillKnowsTheCollection(): void
    {
        $property = service(Compiler::class)->compile(MockEntityWithCollection::class)->property('members');
        $data = $property->__serialize();

        unset($data['collectionClass'], $data['collectionContentType']);

        $stale = clone $property;

        $stale->__unserialize($data);

        self::assertSame(MockEntityCollection::class, $stale->collectionClass);
        self::assertSame(MockEntity::class, $stale->collectionContentType);
    }
}
