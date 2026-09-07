<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{Attributes\Service, CachedImplementorList};

#[Service]
readonly class AfterFlushHandlerManager
{
    private CachedImplementorList $cachedImplementorList;

    public function __construct()
    {
        $this->cachedImplementorList = new CachedImplementorList(Entities\AfterFlushHandler::class);
    }

    public function handle(Snapshots\Changes $changes): bool
    {
        $madeChanges = false;

        foreach ($this->cachedImplementorList->get() as $handler) {
            if ($handler->handle($changes)) {
                $madeChanges = true;
            }
        }

        return $madeChanges;
    }
}
