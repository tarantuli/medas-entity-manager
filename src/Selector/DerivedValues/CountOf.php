<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\DerivedValues;

use Medas\EntityManager\Selector\Definition;

/**
 * How many rows of the definition refer to the selected row; 0 when none do.
 */
class CountOf extends RelatedRows
{
    public static function c(Definition $definition, string $referringProperty, string|null $property = null): static
    {
        return new static($definition, $referringProperty, $property);
    }
}
