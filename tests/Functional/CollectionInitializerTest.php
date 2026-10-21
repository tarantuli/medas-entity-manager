<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\{
    Collections\MockEntityCollection,
    Collections\MockEntityWithCollection,
    MockEntity
};

// A new entity's stored collections start empty, whether it is made through create()
// or with new and then persisted; a collection that was set is left alone.
class CollectionInitializerTest extends BaseTestClass
{
    public function testCreateGivesAnUnsentCollectionAnEmptyOne(): void
    {
        /** @var MockEntityWithCollection $entity */
        $entity = $this->entityManager()->create(
            MockEntityWithCollection::class,
            ['name' => 'created']
        );

        self::assertInstanceOf(MockEntityCollection::class, $entity->members);
        self::assertCount(0, $entity->members);
    }

    public function testPersistGivesAnUnsetCollectionAnEmptyOne(): void
    {
        $entity = new MockEntityWithCollection();

        $entity->name = 'persisted';

        $this->entityManager()->persist($entity);

        self::assertInstanceOf(MockEntityCollection::class, $entity->members);
        self::assertCount(0, $entity->members);
    }

    public function testACollectionThatIsSetIsLeftAlone(): void
    {
        $member = new MockEntity();
        $entity = new MockEntityWithCollection();
        $members = new MockEntityCollection([$member]);
        $entity->name = 'with members';
        $entity->members = $members;

        $this->entityManager()->persist($entity);

        self::assertSame($members, $entity->members);
        self::assertCount(1, $entity->members);
    }
}
