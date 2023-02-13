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
        $changes = new Entities\Changes();

        foreach ($entities as $entity) {
            if ($savedStates[$entity] ?? null) {
                if ($diff = $this->snapshotManager->findChanges($entity, $savedStates[$entity])) {
                    $changes->addUpdate($entity, $diff);
                }
            }
            else {
                $changes->addCreate($entity);
            }
        }

        foreach ($entitiesToDelete as $entity) {
            $changes->addDelete($entity);
        }

        $this->flusher->flush($changes);
    }

    public function setFlusher(Flusher|null $flusher): self
    {
        $this->flusher = $flusher;

        return $this;
    }
}
