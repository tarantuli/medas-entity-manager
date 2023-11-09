<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager, Interfaces\ImplementorFinder};

#[Service]
readonly class BeforeFlushHandlerManager
{
    private const HANDLERS_CACHE_KEY = 'BeforeFlushHandlerManager::handlers';

    public function __construct(
        private CacheManager $cacheManager,
    )
    {
    }

    public function handle(Entities\Changes $changes): bool
    {
        $madeChanges = false;

        foreach ($this->getHandlers() as $handler) {
            $madeChanges = $madeChanges || $handler->handle($changes);
        }

        return $madeChanges;
    }

    /** @return Entities\BeforeFlushHandler[] */
    private function getHandlers(): array
    {
        return $this->cacheManager->get()->get(
            self::HANDLERS_CACHE_KEY,
            fn() => service(ImplementorFinder::class)->find(Entities\BeforeFlushHandler::class)
        );
    }
}
