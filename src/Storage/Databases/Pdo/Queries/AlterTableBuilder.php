<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

use Medas\EntityManager\Storage\Databases\Pdo\Structure\Changes;
use Medas\EntityManager\Storage\Interfaces\Storage;

class AlterTableBuilder
{
    public function __construct(
        private Changes $changes,
    )
    {
    }

    public function create(Storage $storage): Query
    {
        $query = 'alter table ' . $this->changes->name;
        foreach ($this->changes->addFields as $field) {
            $query .= sprintf('add column %s %s,', $field->name, $field->definition);
        }
        foreach ($this->changes->changeFields as $field) {
            $query .= sprintf('modify column %s %s %s,', $field->name, $field->name, $field->definition);
        }
        $query .= substr($query, 0, -1);

        return new Query($query, [], $storage);
    }

}
