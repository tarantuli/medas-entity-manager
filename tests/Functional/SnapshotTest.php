<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\EntityManager\Snapshots\Snapshot;
use Medas\EntityManager\Snapshots\SnapshotManager;
use Medas\Test\BaseTest;
use Medas\Test\MockUps\StoredEntity;

class SnapshotTest extends BaseTest
{
    public function testCreateSnapshot(): StoredEntity
    {
        $snapshotManager = service(SnapshotManager::class);
        $entityManager = $this->entityManager();

        $entity = $entityManager->get(StoredEntity::class, 1);
        $initialSnapshot = $snapshotManager->forEntity($entity);

        self::assertInstanceOf(Snapshot::class, $initialSnapshot);
        self::assertEquals(1, $initialSnapshot->data['id']);

        return $entity;
    }

    /** @depends testCreateSnapshot */
    public function testMakeDiff(StoredEntity $entity): void
    {
        $snapshotManager = service(SnapshotManager::class);
        $initialSnapshot = $snapshotManager->forEntity($entity);

        $entity->name = 'changed name';
        $diff = $snapshotManager->findChanges($entity, $initialSnapshot);

        self::assertIsArray($diff);
        self::assertEquals(['name' => 'changed name'], $diff);
    }
}
