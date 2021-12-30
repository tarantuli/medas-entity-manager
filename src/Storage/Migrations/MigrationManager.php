<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Migrations;

use Medas\Core\Directory;
use Medas\EntityManager\Storage\UnitOfWork\UnitOfWork;
use Medas\EntityManager\Storage\UnitOfWork\UnitOfWorkExecutor;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class MigrationManager
{
    public function __construct(
        private UnitOfWorkExecutor $unitOfWorkExecutor,
    )
    {
    }
    public function migrate(string $directory): void
    {
        Directory::loadPhpFiles($directory);
        $this->processEntities($directory);
    }

    private function processEntities(string $directory)
    {
        $unitOfWork = new UnitOfWork();
        $migrations = $this->findMigrations($directory);

        foreach ($migrations as $migration) {
            $migration->migrate($unitOfWork);
        }

        $this->unitOfWorkExecutor->execute($unitOfWork);
    }

    /** @return Migration[] */
    private function findMigrations(string $directory): array
    {
        $migrations = [];
        foreach (get_declared_classes() as $className) {
            if (null === $migration = $this->getMigration($className, $directory)) {
                continue;
            }

            $migrations[] = $migration;
        }

        return $migrations;

    }

    private function getMigration(string $className, string $directory): Migration|null
    {
        $class = new \ReflectionClass($className);

        if (!$class->getFileName() || !str_starts_with($class->getFileName(), $directory)) {
            return null;
        }

        if (!$class->implementsInterface(Migration::class)) {
            return null;
        }

        return new $className();
    }
}
