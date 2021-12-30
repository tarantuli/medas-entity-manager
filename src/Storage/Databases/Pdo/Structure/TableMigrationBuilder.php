<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Structure;

use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\AlterTableBuilder;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\CreateTableBuilder;
use Medas\EntityManager\Storage\Databases\Pdo\Queries\Query;
use Medas\EntityManager\Storage\Migrations\MigrationBuilder;
use Medas\FileBuilder\PhpClass\MethodDefinition;

class TableMigrationBuilder implements MigrationBuilder
{
    private TableStructureFinder $tableStructureFinder;
    private Database $database;

    public function __construct(
        private ChangeFinder          $changeFinder,
        private EntityStructureFinder $entityStructureFinder,
    )
    {
    }

    public function setDatabase(Database $database): void
    {
        $this->database = $database;
        $this->tableStructureFinder = new TableStructureFinder($database);
    }

    public function build(string $className, MethodDefinition $migrateMethod, MethodDefinition $undoMethod): void
    {
        if (null === $query = $this->buildQuery($className)) {
            return;
        }

        $queryClass = Query::class;

        $migrateMethod->body .= <<<PHP
            \$unitOfWork->addAction(new \\$queryClass("$query->query", [], db("{$this->database->name()}")));
        PHP;

    }

    private function buildQuery(string $className): Query|null
    {
        $expectedStructure = $this->entityStructureFinder->find($className);
        $existingStructure = $this->tableStructureFinder->find($this->database->store($expectedStructure->name));

        if ($existingStructure === null) {
            return (new CreateTableBuilder($expectedStructure))->create($this->database);
        }
        else {
            $changes = $this->changeFinder->find($expectedStructure, $existingStructure);
            return $changes ? (new AlterTableBuilder($changes))->create($this->database) : null;
        }
    }
}
