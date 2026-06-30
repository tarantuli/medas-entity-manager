<?php

declare(strict_types=1);

namespace Medas\EntityManager\Snapshots;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{BeforeFlushHandlerManager, Interfaces\HasSoftDeletes, MetaDataManager};

#[Service]
readonly class ChangeFinder
{
    public function __construct(
        private BeforeFlushHandlerManager $beforeFlushHandlerManager,
        private MetaDataManager           $metaDataManager,
        private SnapshotManager           $snapshotManager,
    )
    {
    }

    public function gather(\Closure $entities, \Closure $savedStates, \Closure $entitiesToDelete): Changes
    {
        $changes = $this->gatherChanges($entities(), $savedStates(), $entitiesToDelete());

        if ($this->beforeFlushHandlerManager->handle($changes)) {
            $changes = $this->gatherChanges($entities(), $savedStates(), $entitiesToDelete());
        }

        return $changes;
    }

    private function gatherChanges(array $entities, \SplObjectStorage $savedStates, array $entitiesToDelete): Changes
    {
        $changes = new Changes();

        foreach ($entitiesToDelete as $i => $entity) {
            $metaData = $this->metaDataManager->get($entity::class);

            if ($metaData->softDeletes) {
                /** @var HasSoftDeletes $entity */
                $entity->softDelete();

                unset($entitiesToDelete[$i]);
            }
        }

        foreach ($entities as $entity) {
            if ($savedStates[$entity] ?? null) {
                if ($diff = $this->snapshotManager->findPropertyChanges($entity, $savedStates[$entity])) {
                    $changes->addUpdate($entity, $diff);
                }
            }
            else {
                $diff = $this->snapshotManager->findPropertyChanges($entity, null);

                $changes->addCreate($entity, $diff);
            }
        }

        foreach ($entitiesToDelete as $entity) {
            $changes->addDelete($entity);
        }

        return $changes;
    }
}
