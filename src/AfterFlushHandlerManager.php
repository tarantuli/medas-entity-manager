<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager, Interfaces\ImplementorFinder};

#[Service]
readonly class AfterFlushHandlerManager
{
    private const HANDLERS_CACHE_KEY = 'AfterFlushHandlerManager::handlers';

    public function __construct(
        private CacheManager $cacheManager,
    )
    {
    }

    public function handle(Snapshots\Changes $changes): bool
    {
        $madeChanges = false;

        foreach ($this->getHandlers() as $handler) {
            $madeChanges = ($madeChanges or $handler->handle($changes));
        }

        return $madeChanges;
    }

    /** @return Entities\AfterFlushHandler[] */
    private function getHandlers(): array
    {
        $names = $this->cacheManager->get()->get(
            self::HANDLERS_CACHE_KEY,
            fn() => servicesToNames(service(ImplementorFinder::class)->find(Entities\AfterFlushHandler::class))
        );

        return namesToServices($names);
    }
}
