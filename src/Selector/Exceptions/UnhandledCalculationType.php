<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\EntityManager\Selector\Calculations\Calculation;

class UnhandledCalculationType extends BaseException
{
    public function __construct(Calculation $calculation)
    {
        parent::__construct($calculation::class);
    }

    public function pattern(): string
    {
        return 'unhandled calcuation of type %s';
    }
}
