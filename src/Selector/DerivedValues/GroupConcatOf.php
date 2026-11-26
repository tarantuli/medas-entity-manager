<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\DerivedValues;

use Medas\EntityManager\Selector\{Calculations\Calculation, Definition};

/**
 * $value of the rows of the definition that refer to the selected row, joined into one
 * text with $separator, in the order of the definition's sorts; null when none do. Such
 * as the names of the products on an order. With $isDistinct, a value that occurs more
 * than once is listed once.
 *
 * The database may cut a long result off: MySQL at group_concat_max_len (1024 bytes by
 * default).
 */
class GroupConcatOf extends RelatedRows
{
    public static function c(
        Definition  $definition,
        string      $referringProperty,
        Calculation $value,
        string      $separator = ', ',
        bool        $isDistinct = false,
        string|null $property = null,
    ): static
    {
        return new static(
            $definition,
            $referringProperty,
            $value,
            $separator,
            $isDistinct,
            $property
        );
    }

    public function __construct(
        Definition         $definition,
        string             $referringProperty,
        public Calculation $value,
        public string      $separator = ', ',
        public bool        $isDistinct = false,
        string|null        $property = null,
    )
    {
        parent::__construct($definition, $referringProperty, $property);
    }
}
