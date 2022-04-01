<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector;

class Parameter implements Element
{
    public static function c(string $name, bool $hasDefault = false, mixed $default = null): static
    {
        return new static($name, $hasDefault, $default);
    }

    public function __construct(
        public string $name,
        public bool   $hasDefault = false,
        public mixed  $default = null
    )
    {
    }
}
