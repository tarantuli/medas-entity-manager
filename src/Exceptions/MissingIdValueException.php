<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class MissingIdValueException extends BaseException
{
    public function __construct(string $propertyName)
    {
        parent::__construct($propertyName);
    }

    public function getPattern(): string
    {
        return 'no id value given for property %s';
    }
}
