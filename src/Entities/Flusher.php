<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

interface Flusher
{
    public function flush(array $entities, \SplObjectStorage $savedStates, array $entitiesToDelete): void;
}
