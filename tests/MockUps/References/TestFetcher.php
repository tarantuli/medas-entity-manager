<?php

declare(strict_types=1);

namespace Medas\Test\MockUps\References;

use Medas\EntityManager\Entities\Fetcher;
use Medas\EntityManager\Entities\FetchResult;
use Medas\EntityManager\MetaData;
use Medas\EntityManager\Selector\Selector;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class TestFetcher implements Fetcher
{

    public function fetchValue(MetaData $metaData, object $entity, MetaData\Property $property): FetchResult
    {
        return new FetchResult(false, null);
    }

    public function fetch(Selector $selector, array $arguments = []): array
    {
        return [];
    }

    public function fetchReferences(MetaData $metaData, object $entity, MetaData\Reference $reference): array
    {
        return [
            em()->get(ChildEntity::class, 1),
            em()->get(ChildEntity::class, 2),
        ];
    }
}
