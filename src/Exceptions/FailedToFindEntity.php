<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class FailedToFindEntity extends BaseException
{
    public function __construct(mixed $phpType, mixed $value)
    {
        parent::__construct($phpType, $value);
    }

    public function pattern(): string
    {
        return 'Failed to find entity of type %s with id %s';
    }
}
