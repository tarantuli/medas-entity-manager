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
        Type|null $type = null
    ): static
    {
        return new static($name, $hasDefault, $default, $type);
    }

    public function __construct(
        public string    $name,
        public bool      $hasDefault = false,
        public mixed     $default = null,
        public Type|null $type = null,
    )
    {
    }
}
