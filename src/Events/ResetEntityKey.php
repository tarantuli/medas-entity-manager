<?php

declare(strict_types=1);

namespace Medas\EntityManager\Events;

class ResetEntityKey
{
    public function __construct(
        public readonly object $entity,
    )
    {
    }
}
