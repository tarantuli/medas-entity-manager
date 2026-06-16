<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager, Interfaces\ImplementorFinder};

#[Service]
readonly class BeforeFlushHandlerManager
{
    /** @var Entities\BeforeFlushHandler[] */
    private array $handlers;

    public function __construct(
        private CacheManager $cacheManager,
        ImplementorFinder    $implementorFinder,
    )
    {
        $classNames = $this->cacheManager->get()->get(
            __CLASS__,
            fn() => $implementorFinder->find(Entities\BeforeFlushHandler::class)
        );

        $this->handlers = namesToServices($classNames);
    }

    public function handle(Snapshots\Changes $changes): bool
    {
        $madeChanges = false;

        foreach ($this->handlers as $handler) {
            $madeChanges = ($madeChanges or $handler->handle($changes));
        }

        return $madeChanges;
    }
}
