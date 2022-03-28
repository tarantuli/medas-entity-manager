<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Operants;

class Argument implements Operant
{
    public function __construct(
        public string $name
    )
    {
    }
}
