<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Exceptions;

use Medas\Core\Exceptions\BaseException;

class UndeclaredParameters extends BaseException
{
    public function __construct(array $names)
    {
        parent::__construct(count($names) === 1 ? '' : 's', implode(', ', $names));
    }

    public function pattern(): string
    {
        return 'found references to undeclared parameter%s: %s';
    }
}
