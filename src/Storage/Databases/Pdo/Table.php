<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

use Medas\ServiceManager\Interfaces\Storage\Record;
use Medas\ServiceManager\Interfaces\Storage\TableBlueprint;

class Table implements \Medas\ServiceManager\Interfaces\Storage\Table
{
    public function __construct(private Database $database, private string $name)
    {
    }

    public function update(TableBlueprint $blueprint): void
    {
        // TODO: Implement update() method.
    }

    public function drop(): void
    {
        $this->database->execute($this->database->queryBuilder()->dropTable($this));
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getRecord(array $filters): Record
    {
        $statement = $this->database->execute(query: $this->database->queryBuilder()->select(
            tables: [$this],
            filters: $filters)
        );
        funcdump(($statement->fetch()));
    }

    public function getName(): string
    {
        return $this->name;
    }
}
