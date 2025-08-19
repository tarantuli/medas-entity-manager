<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Grouping;

use Medas\EntityManager\Selector\{Element, Operants\Operant};

class GroupBy implements Element
{
    public static function c(Operant $operant): static
    {
        return new static($operant);
    }

    public function __construct(
        public Operant $operant,
    )
    {
    }
}
