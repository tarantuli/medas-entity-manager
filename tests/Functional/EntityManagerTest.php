<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\Core\GlobalRepository;
use Medas\EntityManager\EntityManager;
use Medas\EntityManager\Exceptions\{ClassIsNotAnEntity, InvalidPropertyType};
use Medas\EntityManager\FlushManager;
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\{MockEntity, MockFlusher, MockNotAnEntity};

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
        $this->expectException(InvalidPropertyType::class);
        $entityManager->get(MockEntity::class, 'string value');
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
        $flusher = GlobalRepository::objectInstantiator()->instantiate(MockFlusher::class);
        service(FlushManager::class)->setFlusher($flusher);

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
