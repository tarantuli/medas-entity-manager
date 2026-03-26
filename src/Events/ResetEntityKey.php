<?php

declare(strict_types=1);

namespace Medas\EntityManager\Events;

readonly class ResetEntityKey
{
    public function __construct(
        public object $entity,
    )
    {
    }
}
