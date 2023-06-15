<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConsoleCommands;

use Medas\Console\Commands\{BaseConsoleCommand, ConsoleCommandGroup};
use Medas\Console\Formats\Color;
use Medas\Console\Printer;
use Medas\Console\Text;
use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\DirectoryManager;
use Medas\EntityManager\Entities\Generator\{EntityClassGenerator, FileNameFinder};

#[Service]
class CreateEntityFile extends BaseConsoleCommand
{
    public function __construct(
        private readonly DirectoryManager      $directoryManager,
        private readonly Printer               $printer,
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
        $className = $arguments[1];

        $useGuid = ($arguments[2] ?? null) !== '--id';

        $code = $this->entityClassGenerator->generate($className, $useGuid);
        $fileName = $this->fileNameFinder->find($className);

        $this->directoryManager->create(dirname($fileName));

        if (file_exists($fileName)) {
            $this->printer->print(new Text('file ' . $fileName . ' already exists', Color::LightRed));
        }
        elseif (file_put_contents($fileName, $code)) {
            $this->printer->print(new Text('created entity file '), new Text($fileName, Color::LightYellow));
        }
        else {
            $this->printer->print(new Text('could not creat entity file ' . $fileName, Color::Red));
        }
    }
}
