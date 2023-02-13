<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Entities\AfterFlushHandler;
use Medas\EntityManager\Entities\Changes;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Cache\CacheManager;
use function sm;

#[Service]
class AfterFlushHandlerManager
{
    private const HANDLERS_CACHE_KEY = 'AfterFlushHandlerManager::handlers';

    public function __construct(
        private readonly CacheManager $cacheManager,
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
            fn() => sm()->findImplementors(AfterFlushHandler::class)
        );
    }
}
