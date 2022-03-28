<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Operants;

class Value implements Operant
{
    public function __construct(
        public mixed $value
    )
    {
    }
}
