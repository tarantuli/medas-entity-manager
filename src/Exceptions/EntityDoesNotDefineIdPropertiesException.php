<?php

declare(strict_types=1);

namespace EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class EntityDoesNotDefineIdPropertiesException extends BaseException
{
    public function __construct(string $className)
    {
        parent::__construct($$className);
    }

    public function getPattern(): string
    {
        return 'entity class %s does not define id properties';
    }
}
