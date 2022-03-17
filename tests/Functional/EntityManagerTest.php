<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\EntityManager\EntityManager;
use Medas\EntityManager\Exceptions\ClassIsNotAnEntityException;
use Medas\EntityManager\Exceptions\IdValueShouldBeAnArrayException;
use Medas\EntityManager\Exceptions\InvalidPropertyTypeException;
use Medas\EntityManager\Exceptions\NonIdPropertyGivenException;
use Medas\Test\BaseTest;
use Medas\Test\MockUps\MockEntity;
use Medas\Test\MockUps\MockEntityCompositeId;
use Medas\Test\MockUps\MockNotAnEntity;

class EntityManagerTest extends BaseTest
{
    public function testNotAnEntityManager(): void
    {
        $entityManager = $this->entityManager();

        $this->expectException(ClassIsNotAnEntityException::class);
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
        $this->expectException(NonIdPropertyGivenException::class);
        $entityManager->get(MockEntity::class, ['id' => 1, 'name' => 'test']);
    }

    public function testWrongIdType(): void
    {
        $entityManager = $this->entityManager();
        $this->expectException(InvalidPropertyTypeException::class);
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

        $this->expectException(IdValueShouldBeAnArrayException::class);
        $entityManager->get(MockEntityCompositeId::class, 1);
    }

    public function testCreateEntity(): void
    {
        $entityManager = $this->entityManager();

        $entity = $entityManager->create(MockEntity::class, ['name' => 'createTest']);
        self::assertInstanceOf(MockEntity::class, $entity);
    }
}
