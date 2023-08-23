<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\CacheManager;
use Medas\Core\Interfaces\ImplementorFinder;
use Medas\EntityManager\Entities\{BeforeFlushHandler, Changes};

#[Service]
readonly class BeforeFlushHandlerManager
{
    private const HANDLERS_CACHE_KEY = 'BeforeFlushHandlerManager::handlers';

    public function __construct(
        private CacheManager $cacheManager,
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
