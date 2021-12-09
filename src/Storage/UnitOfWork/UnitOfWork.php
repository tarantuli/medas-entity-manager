<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\UnitOfWork;

use Medas\EntityManager\Storage\Databases\Pdo\Queries\Query;

class UnitOfWork
{
    /** @var Query[] */
    public array $creates = [];
    /** @var Query[] */
    public array $updates = [];
}
