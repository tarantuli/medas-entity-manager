<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

interface AfterFlushHandler
{
    public function handle(Changes $changes): void;
}
