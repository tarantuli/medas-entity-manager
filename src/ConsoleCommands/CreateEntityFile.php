<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConsoleCommands;

use Medas\Console\{Commands\ConsoleCommandGroup, Printer};
use Medas\Core\Attributes\Service;
use Medas\EntityManager\BaseFileCreator;
use Medas\EntityManager\Entities\Generator\{EntityClassGenerator, FileNameFinder};
use Medas\FileSystem\DirectoryCreator;

#[Service]
readonly class CreateEntityFile extends BaseFileCreator
{
    public function __construct(
        DirectoryCreator              $directoryCreator,
        Printer                       $printer,
        private EntityClassGenerator  $entityClassGenerator,
        private EntityManagerCommands $entityManagerCommands,
        private FileNameFinder        $fileNameFinder,
    )
    {
        parent::__construct($directoryCreator, $printer);
    }

    public function group(): ConsoleCommandGroup
    {
        return $this->entityManagerCommands;
    }

    public function name(): string
    {
        return 'create-entity-file';
    }

    public function aliases(): array
    {
        return ['c.entity'];
    }

    public function description(): string
    {
        return 'Creates an entity file for the given fully qualified class name';
    }

    public function process(array $arguments): void
    {
        $className = $arguments[1];
        $useUuid = ($arguments[2] ?? null) !== '--id';
        $code = $this->entityClassGenerator->generate($className, $useUuid);
        $fileName = $this->fileNameFinder->find($className);

        $this->writeToFile($code, $fileName);
    }
}
