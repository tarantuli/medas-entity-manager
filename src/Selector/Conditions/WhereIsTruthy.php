<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Conditions;

use Medas\EntityManager\Selector\Operants\Operant;

class WhereIsTruthy implements Condition
{
    public static function c(Operant $property): static
    {
        return new static($property);
    }

    public function __construct(
        public Operant $property,
    )
    {
    }
}
