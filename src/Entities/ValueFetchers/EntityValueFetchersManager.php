<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\ValueFetchers;

use Medas\Core\{Attributes\Service, CachedImplementorList};

#[Service]
readonly class EntityValueFetchersManager
{
    private CachedImplementorList $cachedImplementorList;

    public function __construct()
    {
        $this->cachedImplementorList = new CachedImplementorList(EntityValueFetcher::class);
    }

    /** @return EntityValueFetcher[] */
    public function get(): array
    {
        return $this->cachedImplementorList->get();
    }
}
