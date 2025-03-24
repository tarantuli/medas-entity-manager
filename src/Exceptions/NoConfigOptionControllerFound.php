<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class NoConfigOptionControllerFound extends BaseException
{
    public function pattern(): string
    {
        return 'Found an entity with with a store or storage config option, but no ConfigOptionController was found';
    }
}
