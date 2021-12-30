<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Migrations;

use Medas\Core\Directory;
use Medas\EntityManager\Attributes\Entity;
use Medas\EntityManager\Storage\UnitOfWork\UnitOfWork;
use Medas\FileBuilder\PhpClass\MethodDefinition;
use Medas\FileBuilder\PhpClass\ParameterDefinition;
use Medas\FileBuilder\PhpClass\PhpClassDefinition;
use Medas\FileBuilder\PhpClassBuilder;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class MigrationBuildManager
{
    private PhpClassDefinition $migrationClass;
    private MethodDefinition $migrateMethod;
    private MethodDefinition $undoMethod;

    public function __construct(
        private PhpClassBuilder $phpClassBuilder,
    )
    {
    }

    public function createMigration(string $directory): string
    {
        $this->initializeClass();
        $this->initializeMethods();
        Directory::loadPhpFiles($directory);
        $this->processEntities($directory);

        return $this->phpClassBuilder->build($this->migrationClass);
    }

    private function initializeClass(): void
    {
        $this->migrationClass = new PhpClassDefinition('Migration' . date('YmdHis'), 'Medas\\Migrations');
        $this->migrationClass->implements[] = Migration::class;
    }

    private function initializeMethods(): void
    {
        $this->initializeMigrateMethod();
        $this->initializeUndoMethod();

        $this->migrationClass->methods = [$this->migrateMethod, $this->undoMethod];
    }

    private function initializeMigrateMethod(): void
    {
        $this->migrateMethod = new MethodDefinition('migrate');
        $this->migrateMethod->parameters = [new ParameterDefinition(UnitOfWork::class, 'unitOfWork')];
        $this->migrateMethod->returnTypes = ['void'];
        $this->migrateMethod->body = '';
    }

    private function initializeUndoMethod(): void
    {
        $this->undoMethod = new MethodDefinition('undo');
        $this->undoMethod->parameters = [new ParameterDefinition(UnitOfWork::class, 'unitOfWork')];
        $this->undoMethod->returnTypes = ['void'];
        $this->undoMethod->body = '';
    }

    private function processEntities(string $directory): void
    {
        foreach (get_declared_classes() as $className) {
            if (null === $entity = $this->determineStoredEntity($className, $directory)) {
                continue;
            }

            $this->processEntity($className, $entity);
        }
    }

    private function determineStoredEntity(string $className, string $directory): Entity|null
    {
        $class = new \ReflectionClass($className);

        if (!$class->getFileName() || !str_starts_with($class->getFileName(), $directory)) {
            return null;
        }

        $atttributes = $class->getAttributes(Entity::class);

        if (!$atttributes) {
            return null;
        }

        /** @var Entity $entity */
        $entity = $atttributes[0]->newInstance();

        if ($entity->storage === null && $entity->store === null) {
            return null;
        }

        return $entity;
    }

    private function processEntity(string $className, Entity $entity): void
    {
        storage($entity->storage)->migrationBuilder()
            ->build($className, $this->migrateMethod, $this->undoMethod);
    }
}
