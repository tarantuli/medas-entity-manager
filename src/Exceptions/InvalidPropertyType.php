<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class InvalidPropertyType extends BaseException
{

    public function __construct(string $className, string $propertyName, string $givenType, array $expectedTypes)
    {
        parent::__construct($className, $propertyName, $givenType, $expectedTypes);
    }

    public function pattern(): string
    {
        return 'invalid value given for class %s and property %s: %s instead of %s';
    }
}
