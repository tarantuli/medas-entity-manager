<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class EntityHasMultipleIdProperties extends BaseException
{
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function pattern(): string
    {
        return 'entity class %s has multiple properties marked as ID';
    }

}
