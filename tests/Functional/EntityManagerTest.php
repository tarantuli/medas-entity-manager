<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\EntityManager\EntityManager;
use Medas\EntityManager\Exceptions\ClassIsNotAnEntity;
use Medas\EntityManager\Exceptions\IdValueShouldBeAnArray;
use Medas\EntityManager\Exceptions\InvalidPropertyType;
use Medas\EntityManager\Exceptions\NonIdPropertyGiven;
use Medas\EntityManager\FlushManager;
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\MockEntity;
use Medas\EntityManagerTest\MockUps\MockEntityCompositeId;
use Medas\EntityManagerTest\MockUps\MockFlusher;
use Medas\EntityManagerTest\MockUps\MockNotAnEntity;

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

    public function testTooComplexId(): void
    {
        $entityManager = $this->entityManager();
        $this->expectException(NonIdPropertyGiven::class);
        $entityManager->get(MockEntity::class, ['id' => 1, 'name' => 'test']);
    }

    public function testWrongIdType(): void
    {
        $entityManager = $this->entityManager();
        $this->expectException(InvalidPropertyType::class);
        $entityManager->get(MockEntity::class, 'string value');
    }

    public function testCompositeGetEntity(): void
    {
        $entityManager = $this->entityManager();

        $entity = $entityManager->get(MockEntityCompositeId::class, ['id' => 1, 'name' => 'test']);
        self::assertInstanceOf(MockEntityCompositeId::class, $entity);
        self::assertEquals(1, $entity->id());
    }

    public function testTooSimpleId(): void
    {
        $entityManager = $this->entityManager();

        $this->expectException(IdValueShouldBeAnArray::class);
        $entityManager->get(MockEntityCompositeId::class, 1);
    }

    public function testCreateEntity(): void
    {
        $entityManager = $this->entityManager();

        $entity = $entityManager->create(MockEntity::class, ['name' => 'createTest']);
        self::assertInstanceOf(MockEntity::class, $entity);
    }

    public function testDeleteEntity(): void
    {
        $entityManager = $this->entityManager();
        service(FlushManager::class)->setFlusher(sm()->instantiate(MockFlusher::class));

        $entity1 = $entityManager->get(MockEntity::class, 1);
        $entityManager->get(MockEntity::class, 2);

        self::assertCount(2, $entityManager->getEntities());

        $entityManager->flush();
        self::assertCount(2, $entityManager->getEntities());

        $entityManager->delete($entity1);
        self::assertCount(2, $entityManager->getEntities());

        $entityManager->flush();
        self::assertCount(1, $entityManager->getEntities());
    }
}
