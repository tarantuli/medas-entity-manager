<?php

declare(strict_types=1);

namespace EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class IdValueNotGivenException extends BaseException
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
