<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConsoleCommands;

use Medas\Console\Commands\{BaseConsoleCommand, CommandInput, ConsoleCommandGroup, Range};
use Medas\Core\Attributes\Service;
use Medas\EntityManager\Entities\Generator\{CollectionClassGenerator, FileNameFinder};

#[Service]
readonly class CreateCollectionFile extends BaseConsoleCommand
{
    public function __construct(
        private CollectionClassGenerator $collectionClassGenerator,
        private EntityManagerCommands    $entityManagerCommands,
        private FileNameFinder           $fileNameFinder,
    )
    {
    }

    public function group(): ConsoleCommandGroup
    {
        return $this->entityManagerCommands;
    }

    public function name(): string
    {
        return 'create-collection-file';
    }

    public function aliases(): array
    {
        return ['c.collection'];
    }

    public function description(): string
    {
        return 'Creates an entity collection file for the given fully qualified class name';
    }

    public function allowedArgumentCount(): Range
    {
        return new Range(1);
    }

    public function process(CommandInput $input): void
    {
        $className = $input->getArgument(1);
        $code = $this->collectionClassGenerator->generate($className);
        $fileName = $this->fileNameFinder->find($className . 'Collection');

        $this->fileNameFinder->writeToFile($code, $fileName);
    }
}
