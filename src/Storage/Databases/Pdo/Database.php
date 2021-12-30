<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

use Medas\EntityManager\Storage\Databases\Pdo\Exceptions\DriverNotImplementedException;
use Medas\EntityManager\Storage\Databases\Pdo\Exceptions\PdoDatabaseException;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\BaseSqlQueryBuilder;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\MysqlQueryBuilder;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\QueryBuilder;
use Medas\EntityManager\Storage\Databases\Pdo\Structure\TableMigrationBuilder;
use Medas\EntityManager\Storage\Interfaces\Storage;
use Medas\EntityManager\Storage\Migrations\MigrationBuilder;
use Medas\ServiceManager\Attributes\ConfigValue;

class Database implements Storage
{
    private string $name;
    /** @var Table[] */
    private array $tables = [];
    private QueryBuilder $queryBuilder;
    private \PDO $pdo;
    private \PDOStatement $lastStatement;
    private TableMigrationBuilder $migrationBuilder;

    public function __construct(
        #[ConfigValue('db.pdo.dns')] private string $dns,
        #[ConfigValue('db.pdo.username')] private string $username,
        #[ConfigValue('db.pdo.password')] private string $password,
    )
    {
        $this->initializePdo();
        $this->initializeBuilders();
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

    private function initializeBuilders(): void
    {
        $driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

        $this->queryBuilder = match ($driver) {
            'mysql' => new MysqlQueryBuilder($this),
            'sqlite' => new BaseSqlQueryBuilder($this),
            default => throw new DriverNotImplementedException($driver)
        };

        $this->migrationBuilder = sm()->instantiate(TableMigrationBuilder::class);
        $this->migrationBuilder->setDatabase($this);
    }

    public function stores(): array
    {
        return $this->tables;
    }

    public function store(string $name): Table
    {
        if (!isset($this->tables[$name])) {
            $this->tables[$name] = new Table($this, $name);
        }

        return $this->tables[$name];
    }

    public function deleteStore(string $name): void
    {
        $this->execute($this->queryBuilder->dropTable($name));
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
        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        }
    }

    public function rollbackTransaction(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
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

    public function migrationBuilder(): MigrationBuilder
    {
        return $this->migrationBuilder;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
