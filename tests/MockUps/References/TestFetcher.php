<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\References;

use Medas\EntityManager\Entities\Fetcher;
use Medas\EntityManager\Entities\FetchResult;
use Medas\EntityManager\MetaData;
use Medas\EntityManager\Selector\Selector;
use Medas\Core\Attributes\Service;

#[Service]
readonly class TestFetcher implements Fetcher
{

    public function fetchValue(MetaData $metaData, object $entity, MetaData\Property $property): FetchResult
    {
        return new FetchResult(false, null);
    }

    public function fetch(Selector $selector, array $arguments = []): array
    {
        return [['id' => 1], ['id' => 2]];
    }
}
