<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class IdValueShouldBeAnArray extends BaseException
{
    public function __construct(string $className, string $givenType)
    {
        parent::__construct($className, $givenType);
    }

    public function pattern(): string
    {
        return 'class %s has a complex id field, %s given';
    }
}
