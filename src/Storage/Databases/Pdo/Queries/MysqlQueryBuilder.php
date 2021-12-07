<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

use Medas\EntityManager\Storage\Databases\Pdo\Table;

class MysqlQueryBuilder
{

    /** @param Table[] $tables */
    public function select(array $tables, array $filters): Query
    {
        $query = 'select * from ';
        $arguments = [];
        foreach ($tables as $table) {
            $query .= $table->name . ',';
        }
        $query = substr($query, 0, -1);

        if ($filters) {
            $query .= ' where ';
            foreach ($filters as $field => $value) {
                $query .= $field . '=? and ';
                $arguments[] = $value;
            }

            $query = substr($query, 0, -5);
        }

        return new Query($query, $arguments);
    }
}
