<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\DerivedValues;

use Medas\EntityManager\Selector\{Calculations\Calculation, Definition};

/**
 * The rows of another entity that refer to the selected row, as the subject of an
 * aggregate (CountOf, SumOf, ValueOf).
 *
 * The rows are those of $definition whose $referringProperty equals the selected
 * row's $property (its id when left out). The definition is a scope of its own: a
 * property without an entity is one of the definition's entity, as anywhere else, so
 * an existing selector's definition can be passed in as it is. Only its conditions,
 * and for ValueOf its sorts, are used; anything else in it is refused when the query
 * is built.
 */
abstract class RelatedRows implements Calculation
{
    public function __construct(
        public Definition  $definition,
        public string      $referringProperty,
        public string|null $property = null,
    )
    {
    }
}
