<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\OutputValues;

use Medas\EntityManager\Selector\Calculations\Calculation;

class CalculatedValue implements OutputValue
{
    public static function c(Calculation ...$calculations): static
    {
        return new static(...$calculations);
    }

    /** @var Calculation[] */
    public array $calculations;

    public string|null $alias = null;

    public function __construct(
        Calculation ...$calculations,
    )
    {
        $this->calculations = $calculations;
    }

    public function as(string|null $alias): self
    {
        $this->alias = $alias;

        return $this;
    }
}
