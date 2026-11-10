<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Snapshots\Changes;

interface AfterFlushHandler
{
    /**
     * Returns true when the handler may have created or changed entities, also through the listeners of events it
     * dispatched. EntityManager::flush() then looks for changes again and writes them in the same flush; false tells
     * it nothing came of this handler.
     */
    public function handle(Changes $changes): bool;
}
