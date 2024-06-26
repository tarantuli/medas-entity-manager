<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Operants;

class Values implements Operant
{
    public static function c(array $value): static
    {
        return new static($value);
    }

    public function __construct(
        public array $value,
    )
    {
    }
}
