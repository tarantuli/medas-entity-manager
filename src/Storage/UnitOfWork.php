<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage;

use Medas\EntityManager\Storage\Databases\Pdo\Queries\Query;

class UnitOfWork
{
    /** @var Query[] */
    public array $updates = [];
}
