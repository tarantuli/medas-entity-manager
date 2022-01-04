<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class NonIdPropertyGivenException extends BaseException
{
    public function __construct(string $className, string $property)
    {
        parent::__construct($property, $className);
    }

    public function pattern(): string
    {
        return 'properties %s of class %s are not id properties';
    }
}
