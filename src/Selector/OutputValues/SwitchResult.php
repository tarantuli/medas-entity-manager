<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\OutputValues;

class SwitchResult implements OutputValue
{
    public static function c(mixed ...$cases): static
    {
        return new static(...$cases);
    }

    private array $cases;

    public function __construct(mixed ...$cases)
    {
        $this->cases = $cases;
    }
}
