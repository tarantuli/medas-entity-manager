<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

use Medas\EntityManager\Storage\Databases\Pdo\Exceptions\DriverNotImplementedException;
use Medas\EntityManager\Storage\Databases\Pdo\Exceptions\PdoDatabaseException;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\BaseSqlQueryBuilder;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\MysqlQueryBuilder;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\QueryBuilder;
use Medas\EntityManager\Storage\Interfaces\Storage;
use Medas\ServiceManager\Attributes\ConfigValue;

class Database implements Storage
{
    /** @var Table[] */
    private array $tables = [];
    private QueryBuilder $queryBuilder;
    private \PDO $pdo;
    private \PDOStatement $lastStatement;

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
            'mysql' => new MysqlQueryBuilder($this),
            'sqlite' => new BaseSqlQueryBuilder($this),
            default => throw new DriverNotImplementedException($driver)
        };
    }

    public function getStore(string $name): Table
    {
        if (!isset($this->tables[$name])) {
            $this->tables[$name] = new Table($this, $name);
        }

        return $this->tables[$name];
    }

    public function execute(Queries\Query $query): void
    {
        $this->lastStatement = $this->pdo->prepare($query->query);

        try {
            $this->lastStatement->execute($query->arguments);
        }
        catch (\PDOException $e) {
            throw new PdoDatabaseException($e->getMessage(), $query);
        }

        if ($onComplete = $query->onComplete()) {
            $onComplete($this);
        }
    }

    public function queryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commitTransaction(): void
    {
        $this->pdo->commit();
    }

    public function rollbackTransaction(): void
    {
        $this->pdo->rollBack();
    }

    public function lastGeneratedValue(): int|null
    {
        $id = $this->pdo->lastInsertId();

        return $id === false ? null : (int) $id;
    }

    public function lastStatement(): \PDOStatement
    {
        return $this->lastStatement;
    }

    public function quote(string $identifier): string
    {
        return $this->queryBuilder->quote($identifier);
    }
}
