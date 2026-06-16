<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\ValueFetchers;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager, Interfaces\ImplementorFinder};

#[Service]
class OriginalClassFetcherManager
{
    /** @var OriginalClassFetcher[] */
    private array|null $fetchers = null;

    public function __construct(
        private readonly CacheManager      $cacheManager,
        private readonly ImplementorFinder $implementorFinder,
    )
    {
    }

    /** @return OriginalClassFetcher[] */
    public function get(): array
    {
        if ($this->fetchers === null) {
            $this->loadFetchers();
        }

        return $this->fetchers;
    }

    private function loadFetchers(): void
    {
        $this->fetchers = $this->cacheManager->get()->get(
            __CLASS__,
            fn() => $this->implementorFinder->find(OriginalClassFetcher::class)
        );
    }
}
