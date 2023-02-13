<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

interface Flusher
{
    public function flush(Changes $changes): void;
}
