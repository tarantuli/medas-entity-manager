<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator;

use Composer\Autoload\ClassLoader;
use Medas\Console\{Formats\Color, Printer, Text};
use Medas\Core\Attributes\Service;
use Medas\FileSystem\DirectoryCreator;

#[Service]
readonly class FileNameFinder
{
    public function __construct(
        private ClassNameNormalizer $classNameNormalizer,
        private DirectoryCreator    $directoryCreator,
        private Printer             $printer,
    )
    {
    }

    public function find(string $className): string
    {
        $className = $this->classNameNormalizer->normalize($className);
        $psr4Prefixes = $this->getPsr4Prefixes();

        foreach ($psr4Prefixes as $prefix => $paths) {
            if (!str_starts_with($className, $prefix)) {
                continue;
            }

            $path = strtr($paths[0], '/', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $subClassName = substr($className, strlen($prefix));
            $filePath = $path . strtr($subClassName, '\\', DIRECTORY_SEPARATOR) . '.php';

            return $this->normalizePath($filePath);
        }

        throw new Exceptions\NoPathFoundForClassName($className);
    }

    private function getPsr4Prefixes(): array
    {
        /** @var ClassLoader $loader */
        $loader = require 'vendor/autoload.php';

        return $loader->getPrefixesPsr4();
    }

    private function normalizePath($path): string
    {
        return array_reduce(explode(DIRECTORY_SEPARATOR, $path), function ($a, $b) {
            if ($b === "" || $b === ".") {
                return $a;
            }

            if ($b === "..") {
                return dirname($a);
            }

            return $a === '' && DIRECTORY_SEPARATOR === '\\' ? $b : $a . DIRECTORY_SEPARATOR . $b;
        }, '');
    }

    public function writeToFile(string $code, string $fileName): void
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
