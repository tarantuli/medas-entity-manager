<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

use Medas\EntityManager\Storage\Databases\Pdo\Exceptions\DriverNotImplementedException;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\MysqlQueryBuilder;
use Medas\ServiceManager\Attributes\ConfigValue;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Interfaces\Storage\TableBlueprint;

#[Service]
class Database implements \Medas\ServiceManager\Interfaces\Storage\Database
{
    private array $tables = [];

    private MysqlQueryBuilder $queryBuilder;
    private \PDO $pdo;

    public function __construct(
        #[ConfigValue('db.pdo.dns')] private string $dns,
        #[ConfigValue('db.pdo.username')] private string $username,
        #[ConfigValue('db.pdo.password')] private string $password,
    )
    {
        $this->initializePdo();
        $this->initializeQueryBuilder();
    }

    private function initializePdo(): void
    {
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->pdo = new \PDO($this->dns, $this->username, $this->password, $options);
    }

    private function initializeQueryBuilder(): void
    {
        $driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

        $this->queryBuilder = match ($driver) {
            'mysql' => new MysqlQueryBuilder(),
            default => throw new DriverNotImplementedException($driver)
        };
    }

    public function queryBuilder(): MysqlQueryBuilder
    {
        return $this->queryBuilder;
    }

    public function createTable(TableBlueprint $blueprint): Table
    {
        $query = $this->queryBuilder->createTable($blueprint);
        $this->execute($query);

        return $this->getTable($blueprint->name());
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

    public function execute(Queries\Query $query): \PDOStatement
    {
        try {
            $statement = $this->pdo->prepare($query->getQuery());
            $statement->execute($query->getArguments());
        }
        catch (\PDOException $e) {
            var_dump($query->getQuery(), $query->getArguments());
            throw $e;
        }

        return $statement;
    }
}
