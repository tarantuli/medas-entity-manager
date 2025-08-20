<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\OutputValues;

abstract class BaseOutputValue implements OutputValue
{
    public string|null $alias = null;

    public function as(string|null $alias): self
    {
        $this->alias = $alias;

        return $this;
    }
}
