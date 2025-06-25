<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\ValueFetchers;

use Medas\Core\{Attributes\Service, Interfaces\ImplementorFinder};

#[Service]
readonly class OriginalClassFetcherManager
{
    /** @return OriginalClassFetcher[] */
    public function get(): array
    {
        return cache(__CLASS__, fn() => $this->gather());
    }

    /** @return OriginalClassFetcher[] */
    private function gather(): array
    {
        return service(ImplementorFinder::class)->find(OriginalClassFetcher::class);
    }
}
