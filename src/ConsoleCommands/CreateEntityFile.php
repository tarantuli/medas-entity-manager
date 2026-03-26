<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConsoleCommands;

use Medas\Console\Commands\{Arguments, BaseConsoleCommand, ConsoleCommandGroup, Option};
use Medas\Core\Attributes\Service;
use Medas\EntityManager\Entities\Generator\{EntityClassGenerator, FileNameFinder};

#[Service]
readonly class CreateEntityFile extends BaseConsoleCommand
{
    public function __construct(
        private EntityClassGenerator  $entityClassGenerator,
        private EntityManagerCommands $entityManagerCommands,
        private FileNameFinder        $fileNameFinder,
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

    public function aliases(): array
    {
        return ['c.entity'];
    }

    public function description(): string
    {
        return 'Creates an entity file for the given fully qualified class name';
    }

    public function options(): array
    {
        return [new Option('id')];
    }

    public function process(Arguments $arguments): void
    {
        $className = $arguments->arguments[1];
        $useUuid = $arguments->options['id'] ?? false;
        $code = $this->entityClassGenerator->generate($className, $useUuid);
        $fileName = $this->fileNameFinder->find($className);

        $this->fileNameFinder->writeToFile($code, $fileName);
    }
}
