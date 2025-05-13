<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Console\{Commands\BaseConsoleCommand, Formats\Color, Printer, Text};
use Medas\FileSystem\DirectoryCreator;

abstract readonly class BaseFileCreator extends BaseConsoleCommand
{
    public function __construct(
        private DirectoryCreator $directoryCreator,
        private Printer          $printer,
    )
    {
    }

    protected function writeToFile(string $code, string $fileName): void
    {
        $this->directoryCreator->create(dirname($fileName));

        if (file_exists($fileName)) {
            $this->printer->print(new Text('file ' . $fileName . ' already exists', Color::LightRed));
        }
        elseif (file_put_contents($fileName, $code)) {
            $this->printer->print(
                new Text('created entity file '),
                new Text($fileName, Color::LightYellow)
            );
        }
        else {
            $this->printer->print(new Text('could not creat entity file ' . $fileName, Color::Red));
        }
    }
}
