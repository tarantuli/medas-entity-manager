<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class UnrecognizedUuidFormat extends BaseException
{
    public function __construct(string $name, string $value)
    {
        parent::__construct($name, $value);
    }

    public function pattern(): string
    {
        return 'Unrecognized uuid format for %s: %s';
    }
}
