<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidUuidFormat extends BaseException
{
    public function __construct(string $name, string $value)
    {
        parent::__construct($name, $value);
    }

    public function pattern(): string
    {
        return 'Invalid uuid format for %s: %s';
    }
}
