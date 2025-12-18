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
        $this->flusher?->flush($changes);
        $this->afterFlushHandlerManager->handle($changes);
    }

    public function setFlusher(Entities\Flusher|null $flusher): self
    {
        $this->flusher = $flusher;

        return $this;
    }
}
