<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Selector\Selector;

interface SelectorRecordsFetcher
{
    public function fetch(Selector $selector, array $arguments = []): array;
}
