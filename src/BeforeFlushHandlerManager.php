<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager, Interfaces\ImplementorFinder};

#[Service]
class BeforeFlushHandlerManager
{
    /** @var Entities\BeforeFlushHandler[] */
    private array|null $handlers = null;

    public function __construct(
        private readonly CacheManager      $cacheManager,
        private readonly ImplementorFinder $implementorFinder,
    )
    {
    }

    public function handle(Snapshots\Changes $changes): bool
    {
        if ($this->handlers === null) {
            $this->loadHandlers();
        }

        $madeChanges = false;

        foreach ($this->handlers as $handler) {
            $madeChanges = ($madeChanges or $handler->handle($changes));
        }

        return $madeChanges;
    }

    private function loadHandlers(): void
    {
        $classNames = $this->cacheManager->get()->get(
            __CLASS__,
            fn() => $this->implementorFinder->find(Entities\BeforeFlushHandler::class)
        );

        $this->handlers = namesToServices($classNames);
    }
}
