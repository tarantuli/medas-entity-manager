<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\References;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Entities\{EntityValueFetcher, FetchResult};
use Medas\EntityManager\MetaData;

#[Service]
readonly class TestEntityValueFetcher implements EntityValueFetcher
{
    public function fetch(MetaData $metaData, object $entity, MetaData\Property $property): FetchResult
    {
        return new FetchResult(false, null);
    }
}
