<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Entities\Flusher;
use Medas\EntityManager\Snapshots\SnapshotManager;

#[Service]
class FlushManager
{
    public function __construct(
        private Flusher|null                       $flusher,
        private readonly SnapshotManager           $snapshotManager,
        private readonly AfterFlushHandlerManager  $afterFlushHandlerManager,
        private readonly BeforeFlushHandlerManager $beforeFlushHandlerManager,
    )
    {
    }

    public function flush(\Closure $entities, \Closure $savedStates, \Closure $entitiesToDelete): void
    {
        $changes = $this->gatherChanges($entities(), $savedStates(), $entitiesToDelete());

        if ($this->beforeFlushHandlerManager->handle($changes)) {
            $changes = $this->gatherChanges($entities(), $savedStates(), $entitiesToDelete());
        }

        $this->flusher->flush($changes);

        $this->afterFlushHandlerManager->handle($changes);
    }

    public function setFlusher(Flusher|null $flusher): self
    {
        $this->flusher = $flusher;

        return $this;
    }

    private function gatherChanges(array $entities, \SplObjectStorage $savedStates, array $entitiesToDelete): Entities\Changes
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

        return $changes;
    }
}
