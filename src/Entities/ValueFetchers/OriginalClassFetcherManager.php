<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\ValueFetchers;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager, Interfaces\ImplementorFinder};

#[Service]
readonly class OriginalClassFetcherManager
{
    /** @var OriginalClassFetcher[] */
    private array $fetchers;

    public function __construct(
        private CacheManager $cacheManager,
        ImplementorFinder    $implementorFinder,
    )
    {
        $this->fetchers = $this->cacheManager->get()->get(
            __CLASS__,
            fn() => $implementorFinder->find(OriginalClassFetcher::class)
        );
    }

    /** @return OriginalClassFetcher[] */
    public function get(): array
    {
        return $this->fetchers;
    }
}
