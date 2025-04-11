<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;

#[Service]
class FlushManager
{
    public function __construct(
        private Entities\Flusher|null             $flusher,
        private readonly AfterFlushHandlerManager $afterFlushHandlerManager,
    )
    {
    }

    public function flush(Entities\Changes $changes): void
    {
        $this->flusher->flush($changes);
        $this->afterFlushHandlerManager->handle($changes);
    }

    public function setFlusher(Entities\Flusher|null $flusher): self
    {
        $this->flusher = $flusher;

        return $this;
    }
}
