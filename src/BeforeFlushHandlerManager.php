<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{Attributes\Service, Interfaces\ImplementorFinder};

#[Service]
readonly class BeforeFlushHandlerManager
{
    private const string HANDLERS_CACHE_KEY = 'BeforeFlushHandlerManager::handlers';

    public function handle(Snapshots\Changes $changes): bool
    {
        $madeChanges = false;

        foreach ($this->getHandlers() as $handler) {
            $madeChanges = ($madeChanges or $handler->handle($changes));
        }

        return $madeChanges;
    }

    /** @return Entities\BeforeFlushHandler[] */
    private function getHandlers(): array
    {
        return cache(
            self::HANDLERS_CACHE_KEY,
            fn() => service(ImplementorFinder::class)->find(Entities\BeforeFlushHandler::class)
        );
    }
}
