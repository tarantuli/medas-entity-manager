<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Calculations;

class Literal implements Calculation
{
    public static function c(mixed $literal): static
    {
        return new static($literal);
    }

    public function __construct(
        public mixed $literal,
    )
    {
    }
}
