<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class PropertyHasNoImplicitType extends BaseException
{
    public function __construct(\ReflectionProperty $property)
    {
        parent::__construct($property->class, $property->name);
    }

    public function pattern(): string
    {
        return 'property %s:%s has no implicit type, declare one explicitly using a Property attribute';
    }
}
