<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\OutputValues;

use Medas\EntityManager\Selector\Conditions\Condition;

abstract class ConditionalValue implements OutputValue
{
    public static function c(Condition ...$conditions): static
    {
        return new static(...$conditions);
    }

    /** @var Condition[] */
    protected array $conditions;

    public function __construct(
        Condition ...$conditions,
    )
    {
        $this->conditions = $conditions;
    }
}
