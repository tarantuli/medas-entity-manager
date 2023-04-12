<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\EntityManager\Entities\{Changes, Flusher};
use Medas\ServiceManager\Service;

#[Service]
class MockFlusher implements Flusher
{
    public function flush(Changes $changes): void
    {
        // Do nothing
    }
}
