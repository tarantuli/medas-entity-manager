<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConsoleCommands;

use Medas\Console\Commands\BaseConsoleCommand;
use Medas\Console\Commands\ConsoleCommandGroup;
use Medas\EntityManager\Entities\Generator\EntityClassGenerator;
use Medas\EntityManager\Entities\Generator\FileNameFinder;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class CreateEntityFile extends BaseConsoleCommand
{
    public function __construct(
        private readonly EntityClassGenerator  $entityClassGenerator,
        private readonly EntityManagerCommands $entityManagerCommands,
        private readonly FileNameFinder        $fileNameFinder,
    )
    {
    }

    public function group(): ConsoleCommandGroup
    {
        return $this->entityManagerCommands;
    }

    public function name(): string
    {
        return 'create-entity-file';
    }

    public function description(): string
    {
        return 'Creates an entity file for the given fully qualified class name';
    }

    public function process(array $arguments): void
    {
        $className = $arguments[0];

        $code = $this->entityClassGenerator->generate($className);
        $fileName = $this->fileNameFinder->find($className);

        file_put_contents($fileName, $code);
    }
}
