<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Calculations;

/**
 * The first of the calculations that is not null, such as a sum or else 0.
 */
class Coalesce implements Calculation
{
    public static function c(Calculation ...$calculations): static
    {
        return new static(...$calculations);
    }

    /** @var Calculation[] */
    public array $calculations;

    public function __construct(Calculation ...$calculations)
    {
        $this->calculations = $calculations;
    }
}
