<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CannotCastValueToId extends BaseException
{
    public function __construct(mixed $value)
    {
        parent::__construct($value);
    }

    public function pattern(): string
    {
        return 'cannot cast value %s to id';
    }
}
