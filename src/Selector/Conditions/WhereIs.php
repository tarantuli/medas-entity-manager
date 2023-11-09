<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Conditions;

use Medas\EntityManager\Selector\Operants\Operant;

class WhereIs implements Condition
{
    public static function c(Operant $property, Operant $value): static
    {
        return new static($property, $value);
    }

    public function __construct(
        public Operant $property,
        public Operant $value,
    )
    {
    }
}
