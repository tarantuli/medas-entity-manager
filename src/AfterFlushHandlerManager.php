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

    public function handle(Entities\Changes $changes): void
    {
        foreach ($this->getHandlers() as $handler) {
            $handler->handle($changes);
        }
    }

    /** @return Entities\AfterFlushHandler[] */
    private function getHandlers(): array
    {
        return $this->cacheManager->get()->get(
            self::HANDLERS_CACHE_KEY,
            fn() => service(ImplementorFinder::class)->find(Entities\AfterFlushHandler::class)
        );
    }
}
