<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Operants;

class Argument implements Operant
{
    public static function c(string $name): static
    {
        return new static($name);
    }

    public function __construct(
        public string $name
    )
    {
    }
}
