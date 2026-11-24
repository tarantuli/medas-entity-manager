<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\DerivedValues;

use Medas\Core\Interfaces\Type;
use Medas\EntityManager\Selector\{Calculations\Calculation, Operants\Operant};

/**
 * A value worked out in the query for each selected row, such as a count of related
 * rows: not a stored property, and never set on the entity.
 *
 * Added to a definition, it is selected beside the entity's own columns under its
 * name, and Repository::fetchWithDerivedValues() hands it back next to the entity.
 * Used as an operant, it can be compared and sorted on without being selected:
 *
 *     $count = DerivedValue::c('activeSubscriptionCount', CountOf::c($active, 'tenant'), new Integer());
 *
 *     $definition->add($count, WhereIsAtLeast::c($count, Value::c(1)), SortBy::c($count, SortDirection::DESC));
 *
 * Its type says what the stored representation is turned back into. Its name must not
 * be a property of the selected entity (DerivedValueNameIsAProperty).
 */
class DerivedValue implements Calculation, Operant
{
    public static function c(string $name, Calculation $calculation, Type $type, bool $isNullable = false): static
    {
        return new static($name, $calculation, $type, $isNullable);
    }

    public function __construct(
        public string      $name,
        public Calculation $calculation,
        public Type        $type,

        // Whether a row can have no value, so that "is null" is a meaningful filter on it
        public bool        $isNullable = false,
    )
    {
    }
}
