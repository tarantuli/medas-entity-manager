<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

class Table
{
    public function __construct(
        public Database $database,
        public string   $name,
    )
    {
    }

    public function getRecord(array $filters): Record
    {
        $statement = $this->database->execute(query: $this->database->queryBuilder()->select(
            tables: [$this],
            filters: $filters)
        );

        return new Record($statement->fetch());
    }
}
