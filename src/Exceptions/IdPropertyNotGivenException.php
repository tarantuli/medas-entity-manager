<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class IdPropertyNotGivenException extends BaseException
{
    public function __construct(string $className, string $property)
    {
        parent::__construct($property, $className);
    }

    public function getPattern(): string
    {
        return 'id properties %s of class %s not given';
    }
}
