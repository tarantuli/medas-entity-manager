<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\ValueFetchers;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager, Interfaces\ImplementorFinder};

#[Service]
class EntityValueFetchersManager
{
    /** @var EntityValueFetcher[] */
    private array|null $fetchers = null;

    public function __construct(
        private readonly CacheManager      $cacheManager,
        private readonly ImplementorFinder $implementorFinder,
    )
    {
    }

    /** @return EntityValueFetcher[] */
    public function get(): array
    {
        if ($this->fetchers === null) {
            $this->loadFetchers();
        }

        return $this->fetchers;
    }

    private function loadFetchers(): void
    {
        $classNames = $this->cacheManager->get()->get(
            __CLASS__,
            fn() => $this->implementorFinder->find(EntityValueFetcher::class)
        );

        $this->fetchers = namesToServices($classNames);
    }
}
