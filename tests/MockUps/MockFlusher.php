<?php

declare(strict_types=1);

namespace Medas\Test\MockUps;

use Medas\EntityManager\Entities\Flusher;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class MockFlusher implements Flusher
{
    public function flush(array $entities, \SplObjectStorage $savedStates, array $entitiesToDelete): void
    {
        // Do nothing
    }
}
