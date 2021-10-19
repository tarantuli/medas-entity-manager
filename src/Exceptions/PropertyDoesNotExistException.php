<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class PropertyDoesNotExistException extends BaseException
{
    public function __construct(string $className, string $propertyName)
    {
        parent::__construct($className, $propertyName);
    }

    public function getPattern(): string
    {
        return 'class %s does not have a stored property named %s';
    }
}
