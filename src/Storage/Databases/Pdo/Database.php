<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

use Medas\EntityManager\Storage\Databases\Pdo\Exceptions\DriverNotImplementedException;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\MysqlQueries;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\Queries;
use Medas\ServiceManager\Attributes\EnvValue;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Interfaces\Storage\TableBlueprint;

#[Service]
class Database implements \Medas\ServiceManager\Interfaces\Storage\Database
{
    private array $tables = [];

    private Queries $queries;
    private \PDO $pdo;

    public function __construct(
        #[EnvValue('db.pdo.dns')] private string $dns,
        #[EnvValue('db.pdo.username')] private string $username,
        #[EnvValue('db.pdo.password')] private string $password,
    )
    {
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->pdo = new \PDO($this->dns, $this->username, $this->password, $options);

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

    public function queries(): Queries
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
            $this->tables[$name] = new Table($this, $name);
        }

        return $this->tables[$name];
    }

    public function pdo(): \PDO
    {
        return $this->pdo;
    }
}
