<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;

#[Service]
class FlushManager
{
    public function __construct(
        private readonly AfterFlushHandlerManager $afterFlushHandlerManager,
        private Entities\Flusher|null             $flusher,
    )
    {
    }

    public function flush(Snapshots\Changes $changes): void
    {
        // Without changes there is nothing to write, and nothing for the after-flush
        // handlers either: each of them only reacts to created, updated or deleted
        // entities. A read-only request ends with such a flush, so this spares it the
        // flusher and every after-flush handler.
        if (!$changes->hasChanges()) {
            return;
        }

        $this->flusher?->flush($changes);
        $this->afterFlushHandlerManager->handle($changes);
    }

    public function setFlusher(Entities\Flusher|null $flusher): self
    {
        $this->flusher = $flusher;

        return $this;
    }
}
