<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\OutputValues;

use Medas\EntityManager\Selector\Calculations\Calculation;

interface OutputValue extends Calculation
{
    public function as(string|null $alias): self;
}
