<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\OutputValues;

use Medas\EntityManager\Selector\Calculations\Calculation;

class SwitchCase extends BaseOutputValue
{
    public static function c(Calculation ...$cases): static
    {
        return new static(...$cases);
    }

    /** @var Calculation[] */
    public array $cases;

    public function __construct(Calculation ...$cases)
    {
        $this->cases = $cases;
    }
}
