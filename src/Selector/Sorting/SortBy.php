<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Sorting;

use Medas\EntityManager\Selector\Operants\Operant;

class SortBy
{
    public function __construct(
        public Operant       $operant,
        public SortDirection $direction = SortDirection::ASC,
    )
    {
    }
}
