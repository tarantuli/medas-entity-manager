<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

use Medas\EntityManager\Storage\Databases\Pdo\Exceptions\DriverNotImplementedException;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\MysqlQueries;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\Queries;
use Medas\EntityManager\Storage\TableBlueprint;

class Database implements \Medas\EntityManager\Storage\Interfaces\Database
{
    private array $tables = [];

    private Queries $queries;

    public function __construct(private \PDO $pdo)
    {
        $this->loadQueries();
    }

    private function loadQueries(): void
    {
        $driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

        $this->queries = match ($driver) {
            'mysql' => new MysqlQueries(),
            default => throw new DriverNotImplementedException($driver)
        };
    }

    public function getQueries(): Queries
    {
        return $this->queries;
    }

    public function createTable(TableBlueprint $blueprint): void
    {
        // TODO: Implement createTable() method.
    }

    public function updateTable(TableBlueprint $blueprint): void
    {
        // TODO: Implement updateTable() method.
    }

    public function deleteTable(string $name): void
    {
        // TODO: Implement deleteTable() method.
    }

    public function getTable(string $name): Table
    {
        if (!isset($this->tables[$name])) {
            $this->tables[$name] = new Table($this);
        }

        return $this->tables[$name];
    }
}
