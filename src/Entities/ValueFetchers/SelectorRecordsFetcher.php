<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\ValueFetchers;

use Medas\EntityManager\Selector\Selector;

interface SelectorRecordsFetcher
{
    public function fetch(Selector $selector, array $arguments = []): FetchResult;

    public function fetchCount(Selector $selector, array $arguments = [], bool $ignoreSlice = false): FetchResult;
}
