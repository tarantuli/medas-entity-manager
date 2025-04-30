<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Operants;

class ArgumentArray implements Operant
{
    public static function c(string $name): static
    {
        return new static($name);
    }

    public function __construct(
        public string $name,
    )
    {
    }
}
