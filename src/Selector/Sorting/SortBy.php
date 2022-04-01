<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Sorting;

use Medas\EntityManager\Selector\Operants\Operant;

class SortBy
{
    public static function c(Operant $operant, SortDirection $direction = SortDirection::ASC): static
    {
        return new static($operant, $direction);
    }

    public function __construct(
        public Operant       $operant,
        public SortDirection $direction = SortDirection::ASC,
    )
    {
    }
}
