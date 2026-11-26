<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Calculations;

/**
 * The calculations multiplied, such as a price per unit times the quantity. A value of
 * the row itself, like Addition and Difference.
 */
class Multiplication implements Calculation
{
    public static function c(Calculation ...$factors): static
    {
        return new static(...$factors);
    }

    /** @var Calculation[] */
    public array $factors;

    public function __construct(Calculation ...$factors)
    {
        $this->factors = $factors;
    }
}
