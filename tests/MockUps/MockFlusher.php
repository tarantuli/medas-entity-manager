<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Entities\{Flusher};
use Medas\EntityManager\Snapshots\Changes;

#[Service]
readonly class MockFlusher implements Flusher
{
    public function flush(Changes $changes): void
    {
        // Do nothing
    }
}
