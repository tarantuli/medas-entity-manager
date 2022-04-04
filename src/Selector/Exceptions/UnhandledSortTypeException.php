<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\EntityManager\Selector\Sorting\SortBy;

class UnhandledSortTypeException extends BaseException
{
    public function __construct(SortBy $sort)
    {
        parent::__construct($sort::class);
    }

    public function pattern(): string
    {
        return 'unhandled sort of type %s';
    }
}
