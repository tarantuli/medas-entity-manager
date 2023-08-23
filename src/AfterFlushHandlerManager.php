<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\CacheManager;
use Medas\Core\Interfaces\ImplementorFinder;
use Medas\EntityManager\Entities\{AfterFlushHandler, Changes};

#[Service]
readonly class AfterFlushHandlerManager
{
    private const HANDLERS_CACHE_KEY = 'AfterFlushHandlerManager::handlers';

    public function __construct(
        private CacheManager $cacheManager,
    )
    {
    }

    public function handle(Changes $changes): void
    {
        foreach ($this->getHandlers() as $handler) {
            $handler->handle($changes);
        }
    }

    /** @return AfterFlushHandler[] */
    private function getHandlers(): array
    {
        return $this->cacheManager->get()->get(
            self::HANDLERS_CACHE_KEY,
            fn() => service(ImplementorFinder::class)->find(AfterFlushHandler::class)
        );
    }
}
