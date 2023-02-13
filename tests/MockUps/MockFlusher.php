<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\EntityManager\Entities\Changes;
use Medas\EntityManager\Entities\Flusher;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class MockFlusher implements Flusher
{
    public function flush(Changes $changes): void
    {
        // Do nothing
    }
}
