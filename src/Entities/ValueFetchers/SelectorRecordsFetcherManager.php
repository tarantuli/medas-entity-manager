<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\ValueFetchers;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\{CacheManager, ImplementorFinder};

#[Service]
readonly class SelectorRecordsFetcherManager
{
    public function __construct(
        private CacheManager $cacheManager,
    )
    {
    }

    /** @return SelectorRecordsFetcher[] */
    public function get(): array
    {
        $classNames = $this->cacheManager->get()->get(
            __CLASS__,
            fn() => $this->gather()
        );

        return array_map(fn(string $name) => service($name), $classNames);
    }

    /** @return string[] */
    private function gather(): array
    {
        return array_map(
            fn(object $service) => $service::class,
            service(ImplementorFinder::class)->find(SelectorRecordsFetcher::class)
        );
    }
}
