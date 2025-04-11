<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Snapshots\Changes;

interface Flusher
{
    public function flush(Changes $changes): void;
}
