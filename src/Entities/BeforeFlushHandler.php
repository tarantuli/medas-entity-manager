<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Snapshots\Changes;

interface BeforeFlushHandler
{
    public function handle(Changes $changes): bool;
}
