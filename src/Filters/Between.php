<?php

declare(strict_types=1);

namespace Medas\EntityManager\Filters;

class Between
{
    public function __construct(public mixed $lowerBound, public mixed $upperBound)
    {
    }
}
