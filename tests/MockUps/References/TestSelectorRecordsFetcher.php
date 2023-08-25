<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\References;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Entities\SelectorRecordsFetcher;
use Medas\EntityManager\Selector\Selector;

#[Service]
readonly class TestSelectorRecordsFetcher implements SelectorRecordsFetcher
{
    public function fetch(Selector $selector, array $arguments = []): array
    {
        return [['id' => 1], ['id' => 2]];
    }
}
