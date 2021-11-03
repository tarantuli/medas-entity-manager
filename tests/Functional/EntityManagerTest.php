<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\EntityManager\EntityManager;
use Medas\EntityManager\Exceptions\ClassIsNotAnEntityException;
use Medas\EntityManager\Exceptions\IdValueShouldBeAnArrayException;
use Medas\EntityManager\Exceptions\IdValueShouldBeAScalarException;
use Medas\EntityManager\Exceptions\InvalidPropertyTypeException;
use Medas\Test\BaseTestCase;
use Medas\Test\MockUps\MockEntity;
use Medas\Test\MockUps\MockEntityCompositeId;
use Medas\Test\MockUps\MockNotAnEntity;
use Symfony\Contracts\Cache\CacheInterface;

class EntityManagerTest extends BaseTestCase
{
    public function testNotAnEntityManager(): void
    {
        $entityManager = $this->getEntityManager();

        $this->expectException(ClassIsNotAnEntityException::class);
        $entityManager->get(MockNotAnEntity::class, 1);
    }

    public function testEntityManager(): void
    {
        $entityManager = $this->getEntityManager();

        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }

    public function testGetEntity(): void
    {
        $entityManager = $this->getEntityManager();

        $entity = $entityManager->get(MockEntity::class, 1);
        $this->assertInstanceOf(MockEntity::class, $entity);
        $this->assertEquals(1, $entity->getId());
    }

    public function testTooComplexId(): void
    {
        $entityManager = $this->getEntityManager();
        $this->expectException(IdValueShouldBeAScalarException::class);
        $entityManager->get(MockEntity::class, ['id' => 1, 'name' => 'test']);
    }

    public function testWrongIdType(): void
    {
        $entityManager = $this->getEntityManager();
        $this->expectException(InvalidPropertyTypeException::class);
        $entityManager->get(MockEntity::class, 'test');
    }

    public function testCompositeGetEntity(): void

    {
        $entityManager = $this->getEntityManager();

        $entity = $entityManager->get(MockEntityCompositeId::class, ['id' => 1, 'name' => 'test']);
        $this->assertInstanceOf(MockEntityCompositeId::class, $entity);
        $this->assertEquals(1, $entity->getId());
    }

    public function testTooSimpleId(): void
    {
        $entityManager = $this->getEntityManager();

        $this->expectException(IdValueShouldBeAnArrayException::class);
        $entityManager->get(MockEntityCompositeId::class, 1);
    }
}
