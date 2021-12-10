<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

use Medas\EntityManager\Storage\Interfaces\Action;
use Medas\EntityManager\Storage\Interfaces\Store;

interface QueryBuilder
{
    public function create(Store $store, array $values): Action;

    public function update(Store $store, array $updates, array $conditions): Action;

    /** @param Store[] $stores */
    public function select(array $stores, array $filters): Action;
}
