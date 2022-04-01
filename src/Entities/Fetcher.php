<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\MetaData;
use Medas\EntityManager\Selector\Selector;

interface Fetcher
{
    public function fetch(MetaData $metaData, object $entity, MetaData\Property $property): FetchResult;

    public function fetchRecord(Selector $selector, array $arguments = []): array|null;

    public function fetchAll(Selector $selector = null, array $arguments = []): array;
}
