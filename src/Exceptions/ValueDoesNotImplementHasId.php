<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ValueDoesNotImplementHasId extends BaseException
{
    public function __construct(mixed $value)
    {
        parent::__construct(get_debug_type($value));
    }

    public function pattern(): string
    {
        return 'value type %s does not implement HasId';
    }
}
