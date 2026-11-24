<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Calculations;

/**
 * $minuend minus $subtrahend, such as the capacity minus what is available.
 */
class Difference implements Calculation
{
    public static function c(Calculation $minuend, Calculation $subtrahend): static
    {
        return new static($minuend, $subtrahend);
    }

    public function __construct(
        public Calculation $minuend,
        public Calculation $subtrahend,
    )
    {
    }
}
