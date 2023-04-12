<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Entities\{BeforeFlushHandler, Changes};
use Medas\ServiceManager\Cache\CacheManager;
use Medas\ServiceManager\Mapping\ImplementorFinder;
use Medas\ServiceManager\Service;

#[Service]
class BeforeFlushHandlerManager
{
    private const HANDLERS_CACHE_KEY = 'BeforeFlushHandlerManager::handlers';

    public function __construct(
        private readonly CacheManager $cacheManager,
    )
    {
    }

    public function handle(Changes $changes): bool
    {
        $madeChanges = false;

        foreach ($this->getHandlers() as $handler) {
            $madeChanges = $madeChanges || $handler->handle($changes);
        }

        return $madeChanges;
    }

    /** @return BeforeFlushHandler[] */
    private function getHandlers(): array
    {
        return $this->cacheManager->get()->get(
            self::HANDLERS_CACHE_KEY,
            fn() => service(ImplementorFinder::class)->find(BeforeFlushHandler::class)
        );
    }
}
