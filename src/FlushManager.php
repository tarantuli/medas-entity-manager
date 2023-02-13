<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Entities\Flusher;
use Medas\EntityManager\Snapshots\SnapshotManager;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class FlushManager
{
    public function __construct(
        private Flusher|null             $flusher,
        private readonly SnapshotManager $snapshotManager,
    )
    {
    }

    public function flush(array $entities, \SplObjectStorage $savedStates, array $entitiesToDelete): void
    {
        $entitiesToCreate = [];
        $entitiesToUpdate = [];

        foreach ($entities as $entity) {
            if ($savedStates[$entity] ?? null) {
                if ($this->snapshotManager->findChanges($entity, $savedStates[$entity])) {
                    $entitiesToUpdate[] = $entity;
                }
            }
            else {
                $entitiesToCreate[] = $entity;
            }
        }

        $this->flusher->flush($entitiesToCreate, $entitiesToUpdate, $entitiesToDelete);
    }

    public function setFlusher(Flusher|null $flusher): self
    {
        $this->flusher = $flusher;

        return $this;
    }
}
