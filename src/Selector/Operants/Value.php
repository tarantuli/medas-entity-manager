<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Operants;

class Value implements Operant
{
    public static function c(mixed $value): static
    {
        return new static($value);
    }

    public function __construct(
        public mixed $value
    )
    {
    }
}
