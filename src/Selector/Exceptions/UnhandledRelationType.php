<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\EntityManager\Selector\Relations\Relation;

class UnhandledRelationType extends BaseException
{
    public function __construct(Relation $relation)
    {
        parent::__construct($relation::class);
    }

    public function pattern(): string
    {
        return 'unhandled relation of type %s';
    }
}
