<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\MetaData;

interface Fetcher
{
    public function fetch(MetaData $metaData, object $entity, MetaData\Property $property): FetchResult;

    public function fetchRecord(MetaData $metaData, array $conditions): array|null;

    public function fetchAll(MetaData $metaData, array $conditions): array;
}
