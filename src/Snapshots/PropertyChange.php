<?php

declare(strict_types=1);

namespace Medas\EntityManager\Snapshots;

class PropertyChange
{
    public function __construct(
        public mixed $previous,
        public mixed $current,
    )
    {
    }
}
