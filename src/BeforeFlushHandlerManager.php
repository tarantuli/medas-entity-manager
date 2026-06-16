<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{Attributes\Service, CachedImplementorList};

#[Service]
readonly class BeforeFlushHandlerManager
{
    private CachedImplementorList $cachedImplementorList;

    public function __construct()
    {
        $this->cachedImplementorList = new CachedImplementorList(Entities\BeforeFlushHandler::class);
    }

    public function handle(Snapshots\Changes $changes): bool
    {
        $madeChanges = false;

        foreach ($this->cachedImplementorList->get() as $handler) {
            $madeChanges = ($madeChanges or $handler->handle($changes));
        }

        return $madeChanges;
    }
}
