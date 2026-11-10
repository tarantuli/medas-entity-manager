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

    /** Whether the after-flush handlers may have created or changed entities -- see AfterFlushHandler::handle(). */
    public function flush(Snapshots\Changes $changes): bool
    {
        // Without changes there is nothing to write, and nothing for the after-flush
        // handlers either: each of them only reacts to created, updated or deleted
        // entities. A read-only request ends with such a flush, so this spares it the
        // flusher and every after-flush handler.
        if (!$changes->hasChanges()) {
            return false;
        }

        $this->flusher?->flush($changes);

        // Before the handlers: a flush one of them starts must already diff against what was written
        foreach ([...$changes->createdEntities(), ...$changes->updatedEntities()] as $entity) {
            if ($snapshot = $changes->entitySnapshot($entity)) {
                $this->snapshotManager->refresh($entity, $snapshot);
            }
        }

        return $this->afterFlushHandlerManager->handle($changes);
    }

    public function setFlusher(Entities\Flusher|null $flusher): self
    {
        $this->flusher = $flusher;

        return $this;
    }
}
