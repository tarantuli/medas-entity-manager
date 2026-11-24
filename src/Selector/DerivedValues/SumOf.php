<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\DerivedValues;

use Medas\EntityManager\Selector\{Calculations\Calculation, Definition};

/**
 * The sum of $value over the rows of the definition that refer to the selected row;
 * null when none do, so wrap it in Coalesce for 0.
 */
class SumOf extends RelatedRows
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
