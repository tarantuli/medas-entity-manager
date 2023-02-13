<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class PropertyDoesNotExist extends BaseException
{
    public function __construct(public string $className, public string $propertyName)
    {
        parent::__construct($className, $propertyName);
    }

    public function pattern(): string
    {
        return 'class %s does not have a stored property named %s';
    }
}
