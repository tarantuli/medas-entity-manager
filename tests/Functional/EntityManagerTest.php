<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\EntityManager\EntityManager;
use Medas\ServiceManager\ServiceManager;
use Medas\Test\MockUps\MockEntity;
use PHPUnit\Framework\TestCase;

class EntityManagerTest extends TestCase
{
    public function testEntityManager(): void
    {
        $entityManager = $this->getEntityManager();

        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }

    private function getEntityManager(): EntityManager
    {
        $serviceManager = ServiceManager::get();
        $serviceManager->addSourceDirectory(realpath(__DIR__ . '/../../src'));

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $serviceManager->resolve(EntityManager::class);
    }

    public function testGetEntity(): void
    {
        $entityManager = $this->getEntityManager();

        $entity = $entityManager->get(MockEntity::class, 1);
        $this->assertInstanceOf(MockEntity::class, $entity);
        $this->assertEquals(1, $entity->getId());
    }
}
