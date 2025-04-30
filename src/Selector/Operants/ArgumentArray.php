<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Operants;

class ArgumentArray implements Operant
{
    public static function c(string $name, array $value): static
    {
        return new static($name, $value);
    }

    public function __construct(
        public string $name,
        public array  $value,
    )
    {
    }
}
