<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\EntityManager\Selector\Operants\Operant;

class UnhandledOperantTypeException extends BaseException
{
    public function __construct(Operant $operant)
    {
        parent::__construct($operant::class);
    }

    public function pattern(): string
    {
        return 'unhandled operant of type %s';
    }
}
