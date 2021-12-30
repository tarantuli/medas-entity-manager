<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ClassIsNotAnEntityException extends BaseException
{
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function pattern(): string
    {
        return 'class %s is not an entity class';
    }
}
