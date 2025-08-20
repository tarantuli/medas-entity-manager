<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\OutputValues;

use Medas\EntityManager\Selector\Calculations\Calculation;

class SwitchCase extends BaseOutputValue
{
    public static function c(mixed ...$cases): static
    {
        return new static(...$cases);
    }

    /** @var Calculation[]|Calculation[][]} */
    public array $cases;

    public function __construct(mixed ...$cases)
    {
        $this->cases = $cases;
    }
}
