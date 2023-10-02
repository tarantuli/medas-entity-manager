<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\ValueFetchers;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\{CacheManager, ImplementorFinder};

#[Service]
readonly class OriginalClassFetcherManager
{
    public function __construct(
        private CacheManager $cacheManager,
    )
    {
    }

    /** @return OriginalClassFetcher[] */
    public function get(): array
    {
        return $this->cacheManager->get()->get(
            __CLASS__,
            fn() => $this->gather()
        );
    }

    /** @return OriginalClassFetcher[] */
    private function gather(): array
    {
        return service(ImplementorFinder::class)->find(OriginalClassFetcher::class);
    }
}
