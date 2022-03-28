<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Operants;

class Property implements Operant
{
    public function __construct(
        public string      $name,
        public string|null $entity = null,
    )
    {
    }
}
