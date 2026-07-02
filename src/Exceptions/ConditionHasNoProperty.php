<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ConditionHasNoProperty extends BaseException
{
    public function __construct()
    {
        parent::__construct();
    }

    public function pattern(): string
    {
        return 'the condition must have a property to check for null';
    }
}
