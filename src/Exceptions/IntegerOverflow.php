<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class IntegerOverflow extends BaseException
{
    public function __construct(string $name, $value)
    {
        parent::__construct($name, $value);
    }

    public function pattern(): string
    {
        return 'Integer overflow when casting for %s: %s';
    }
}
