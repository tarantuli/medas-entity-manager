<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

class MysqlQueries implements Queries
{

    public function getByValues(string $tableName, array $values): array
    {
        $query = sprintf('select * from %s where 1', $tableName);
        $parameters = [];

        foreach ($values as $name => $value) {
            $query .= sprintf(' and %1$s=:%1$s', $name);
            $parameters[$name] = $value;
        }

        return [$query, $parameters];
    }
}
