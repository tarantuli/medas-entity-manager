<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Grouping;

use Medas\EntityManager\Selector\{Element, Operants\Property};

class GroupBy implements Element
{
    public static function c(Property $property): static
    {
        return new static($property);
    }

    public function __construct(
        public Property $property,
    )
    {
    }
}
