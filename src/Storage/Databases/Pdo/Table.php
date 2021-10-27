<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

class Table implements \Medas\EntityManager\Storage\Interfaces\Table
{
    public function __construct(private Database $database)
    {
    }

    public function getByValues(array $values): RecordCollection
    {
        // TODO: Implement getByValues() method.
    }
}
