<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\{Exceptions\BaseException, Interfaces\PropertyHandler};

class InvalidHandler extends BaseException
{
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function pattern(): string
    {
        return 'property handler %s should be a service implementing ' . PropertyHandler::class;
    }
}
