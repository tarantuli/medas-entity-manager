<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

interface Queries
{

    public function getByValues(string $tableName, array $values): array;
}
