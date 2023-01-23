<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator;

use Medas\EntityManager\ConfigOptions\GeneratorRootNamespace;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\ConfigOptions\ConfigValue;

#[Service]
class ClassNameNormalizer
{
    public function __construct(
        #[ConfigValue(GeneratorRootNamespace::class)]
        private readonly string|null $rootNamespace,
    )
    {
    }

    public function normalize(string $className): string
    {
        // Replace a leading dot by the root namespace
        if (str_starts_with($className, '.')) {
            $className = $this->rootNamespace . substr($className, 1);
        }

        // Replace forward slashes by backward slashes
        return str_replace('/', '\\', $className);
    }
}
