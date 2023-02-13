<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\EntityManager\Selector\Conditions\Condition;

class UnhandledConditionType extends BaseException
{
    public function __construct(Condition $condition)
    {
        parent::__construct($condition::class);
    }

    public function pattern(): string
    {
        return 'unhandled condition of type %s';
    }
}
