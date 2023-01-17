<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator;

use Composer\Autoload\ClassLoader;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class FileNameFinder
{
    public function find(string $className): string|null
    {
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

        return null;
    }

    private function getPsr4Prefixes(): array
    {
        /** @var ClassLoader $loader */
        $loader = require __DIR__ . '/../../../vendor/autoload.php';
        return $loader->getPrefixesPsr4();
    }

    private function normalizePath($path): string
    {
        return array_reduce(explode(DIRECTORY_SEPARATOR, $path), function ($a, $b) {
            if ($b === "" || $b === ".")
                return $a;

            if ($b === "..")
                return dirname($a);

            return $a === '' && DIRECTORY_SEPARATOR === '\\' ? $b : $a . DIRECTORY_SEPARATOR . $b;
        }, '');
    }
}
