<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class PropertyHasMultipleImplicitTypesException extends BaseException
{
    public function __construct(\ReflectionProperty $property)
    {
        parent::__construct($property->class, $property->name);
    }

    public function pattern(): string
    {
        return 'property %s:%s has multiple implicit types, declare one explicitly using a Property attribute';
    }
}
