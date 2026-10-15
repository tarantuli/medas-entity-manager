<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Operants;

use Medas\EntityManager\Selector\Calculations\Calculation;

/**
 * Also a calculation, so it can be aggregated: Max::c(Property::c('role'))
 */
class Property implements Operant, Calculation
{
    public static function c(string $name, string|null $entity = null): static
    {
        return new static($name, $entity);
    }

    public function __construct(
        public string      $name,
        public string|null $entity = null,
    )
    {
    }
}
