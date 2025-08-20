<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\OutputValues;

use Medas\EntityManager\Selector\Calculations\Calculation;

class CalculatedValue extends BaseOutputValue
{
    public static function c(Calculation ...$calculations): static
    {
        return new static(...$calculations);
    }

    /** @var Calculation[] */
    public array $calculations;

    public function __construct(
        Calculation ...$calculations,
    )
    {
        $this->calculations = $calculations;
    }
}
