<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class EntityCollectionDoesNotImplementManagedCollection extends BaseException
{
    public function __construct(\ReflectionClass $class)
    {
        parent::__construct($class->name);
    }

    public function pattern(): string
    {
        return 'class %s is tagged as an EntityCollection, but does not implement ManagedCollection';
    }
}
