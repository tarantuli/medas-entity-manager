<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class UnknownProperties extends BaseException
{
    public function __construct(string $class, array $properties)
    {
        parent::__construct($properties, $class);
    }

    public function pattern(): string
    {
        return 'Unknown properties %s for class %s';
    }
}
