<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

use Medas\EntityManager\Storage\Interfaces\Store;

class Table implements Store
{
    public function __construct(
        public Database $database,
        public string   $name,
    )
    {
    }

    public function getRecord(array $filters): Record
    {
        $query = $this->database->actionBuilder()->select(
            stores: [$this],
            filters: $filters);
        $this->database->execute(query: $query
        );

        return new Record($this->database->lastStatement()->fetch());
    }
}
