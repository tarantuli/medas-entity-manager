<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\{
    EntityManager,
    EntityManagerContext,
    Exceptions\ClassIsNotAnEntity,
    Exceptions\InvalidNumericValue
};
use Medas\EntityManagerTest\{BaseTestClass, MockUps\MockEntity, MockUps\MockNotAnEntity};

class EntityManagerTest extends BaseTestClass
{
    public function testNotAnEntityManager(): void
    {
        $entityManager = $this->entityManager();

        $this->expectException(ClassIsNotAnEntity::class);

        $entityManager->get(MockNotAnEntity::class, 1);
    }

    public function testEntityManager(): void
    {
        $entityManager = $this->entityManager();

        self::assertInstanceOf(EntityManager::class, $entityManager);
    }

    public function testGetEntity(): void
    {
        $entityManager = $this->entityManager();
        $entity = $entityManager->get(MockEntity::class, 1);

        self::assertInstanceOf(MockEntity::class, $entity);
        self::assertEquals(1, $entity->id());
    }

    public function testWrongIdType(): void
    {
        $entityManager = $this->entityManager();

        $this->expectException(InvalidNumericValue::class);

        $entityManager->get(MockEntity::class, 'string value');
    }

    public function testCreateEntity(): void
    {
        $entityManager = $this->entityManager();
        $entity = $entityManager->create(MockEntity::class, ['name' => 'createTest']);

        self::assertInstanceOf(MockEntity::class, $entity);
    }

    // Flushes go to MockFlusher, which the FlushManager service is given as the only
    // Flusher there is.
    public function testDeleteEntity(): void
    {
        $context = new EntityManagerContext();
        $entityManager = $this->entityManager($context);
        $entity1 = $entityManager->get(MockEntity::class, 1);

        $entityManager->get(MockEntity::class, 2);

        self::assertCount(2, $context->entities);

        $entityManager->flush();

        self::assertCount(2, $context->entities);

        $entityManager->delete($entity1);

        self::assertCount(2, $context->entities);

        $entityManager->flush();

        self::assertCount(1, $context->entities);
    }
}
