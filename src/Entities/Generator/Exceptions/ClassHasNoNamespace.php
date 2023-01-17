<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ClassHasNoNamespace extends BaseException
{
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function pattern(): string
    {
        return 'class %s has no namespace';
    }
}
