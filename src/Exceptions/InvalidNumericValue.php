<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidNumericValue extends BaseException
{
    public function __construct(string $name, mixed $value)
    {
        parent::__construct($name, $value);
    }

    public function pattern(): string
    {
        return 'Invalid numeric value for %s: %s';
    }
}
