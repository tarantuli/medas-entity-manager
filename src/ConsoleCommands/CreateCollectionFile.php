<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConsoleCommands;

use Medas\Console\{Commands\ConsoleCommandGroup, Printer};
use Medas\Core\Attributes\Service;
use Medas\EntityManager\BaseFileCreator;
use Medas\EntityManager\Entities\Generator\{CollectionClassGenerator, FileNameFinder};
use Medas\FileSystem\DirectoryCreator;

#[Service]
readonly class CreateCollectionFile extends BaseFileCreator
{
    public function __construct(
        DirectoryCreator                 $directoryCreator,
        Printer                          $printer,
        private CollectionClassGenerator $collectionClassGenerator,
        private EntityManagerCommands    $entityManagerCommands,
        private FileNameFinder           $fileNameFinder,
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

    public function process(array $arguments): void
    {
        $className = $arguments[1];
        $code = $this->collectionClassGenerator->generate($className);
        $fileName = $this->fileNameFinder->find($className . 'Collection');

        $this->writeToFile($code, $fileName);
    }
}
