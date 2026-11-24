<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

// A derived value is selected under its name beside the entity's columns: under a
// property's name it would replace that column's value in the row.
class DerivedValueNameIsAProperty extends BaseException
{
    public function __construct(public string $className, public string $name)
    {
        parent::__construct($className, $name);
    }

    public function pattern(): string
    {
        return 'class %s has a property named %s, which a derived value cannot be named';
    }
}
