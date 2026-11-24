<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\DerivedValues;

use Medas\EntityManager\Selector\{Calculations\Calculation, Definition};

/**
 * $value of the first row of the definition, in the order of its sorts, that refers to
 * the selected row; null when none does. Such as the state of a subscription's next
 * term: the terms after today, sorted by start date.
 */
class ValueOf extends RelatedRows
{
    public static function c(
        Definition  $definition,
        string      $referringProperty,
        Calculation $value,
        string|null $property = null,
    ): static
    {
        return new static($definition, $referringProperty, $value, $property);
    }

    public function __construct(
        Definition         $definition,
        string             $referringProperty,
        public Calculation $value,
        string|null        $property = null,
    )
    {
        parent::__construct($definition, $referringProperty, $property);
    }
}
