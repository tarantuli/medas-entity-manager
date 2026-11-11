<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;

#[Service]
class FlushManager
{
    public function __construct(
        private readonly AfterFlushHandlerManager  $afterFlushHandlerManager,
        private Entities\Flusher|null              $flusher,
        private readonly Snapshots\SnapshotManager $snapshotManager,
    )
    {
    }

    /**
     * Writes $changes, and takes what was written as the baseline the snapshots diff against. When the flusher throws,
     * the write was rolled back and the snapshots are left as they were.
     */
    public function write(Snapshots\Changes $changes): void
    {
        $this->flusher?->flush($changes);

        // Before the handlers: a flush one of them starts must already diff against what was written
        foreach ([...$changes->createdEntities(), ...$changes->updatedEntities()] as $entity) {
            if ($snapshot = $changes->entitySnapshot($entity)) {
                $this->snapshotManager->refresh($entity, $snapshot);
            }
        }
    }

    /** Whether the after-flush handlers may have created or changed entities -- see AfterFlushHandler::handle(). */
    public function handle(Snapshots\Changes $changes): bool
    {
        return $this->afterFlushHandlerManager->handle($changes);
    }

    public function setFlusher(Entities\Flusher|null $flusher): self
    {
        $this->flusher = $flusher;

        return $this;
    }
}
