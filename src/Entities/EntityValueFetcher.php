<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\MetaData;

interface EntityValueFetcher
{
    public function fetch(MetaData $metaData, object $entity, MetaData\Property $property): FetchResult;

    /**
     * Clear any cached data
     */
    public function clearCaches(): void;
}
