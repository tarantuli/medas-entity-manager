<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

use Medas\EntityManager\Storage\Databases\Pdo\Table;

interface QueryBuilder
{
    public function create(Table $table, array $values): Query;

    public function update(Table $table, array $updates, array $conditions): Query;

    /** @param Table[] $tables */
    public function select(array $tables, array $filters): Query;

    public function showCreate(Table $table): Query;
}
