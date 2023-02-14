<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Entities\{BeforeFlushHandler, Changes};
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Cache\CacheManager;

#[Service]
class BeforeFlushHandlerManager
{
    private const HANDLERS_CACHE_KEY = 'BeforeFlushHandlerManager::handlers';

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

    /** @return BeforeFlushHandler[] */
    private function getHandlers(): array
    {
        return $this->cacheManager->get()->get(
            self::HANDLERS_CACHE_KEY,
            fn() => sm()->findImplementors(BeforeFlushHandler::class)
        );
    }
}
