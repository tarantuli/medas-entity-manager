<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;

#[Service]
readonly class ChangeFinder
{
    public function __construct(
        private Snapshots\SnapshotManager $snapshotManager,
        private BeforeFlushHandlerManager $beforeFlushHandlerManager,
    )
    {
    }

    public function gather(\Closure $entities, \Closure $savedStates, \Closure $entitiesToDelete): Entities\Changes
    {
        $changes = $this->gatherChanges($entities(), $savedStates(), $entitiesToDelete());

        if ($this->beforeFlushHandlerManager->handle($changes)) {
            $changes = $this->gatherChanges($entities(), $savedStates(), $entitiesToDelete());
        }

        return $changes;
    }

    private function gatherChanges(
        array             $entities,
        \SplObjectStorage $savedStates,
        array             $entitiesToDelete
    ): Entities\Changes
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
