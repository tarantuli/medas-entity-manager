<?php

declare(strict_types=1);

namespace EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ClassIsNotAnEntityException extends BaseException
{
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function getPattern(): string
    {
        return 'class %s is not an entity class';
    }
}
