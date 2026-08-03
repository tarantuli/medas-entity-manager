<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector;

use Medas\Core\Interfaces\Type;

class Parameter implements Element
{
    public static function c(
        string    $name,
        bool      $hasDefault = false,
        mixed     $default = null,
        Type|null $type = null,
        bool      $allowUnused = false,
    ): static
    {
        return new static($name, $hasDefault, $default, $type, $allowUnused);
    }

    public function __construct(
        public string    $name,
        public bool      $hasDefault = false,
        public mixed     $default = null,
        public Type|null $type = null,

        // Permits this parameter to be declared without any condition
        // referencing it, without tripping the ThrowOnUnusedParameters check.
        // Set it for a "pure gate" parameter - one whose presence in the
        // request selects a query branch (e.g., an `active` flag a selector
        // is applied on) but which no condition actually binds. Without this,
        // such a parameter is indistinguishable from the stale or mistyped
        // leftover that check is meant to catch.
        public bool      $allowUnused = false,
    )
    {
    }
}
