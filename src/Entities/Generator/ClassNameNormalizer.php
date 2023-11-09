<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\EntityManager\ConfigOptions\GeneratorRootNamespace;

#[Service]
readonly class ClassNameNormalizer
{
    public function __construct(
        #[ConfigValue(GeneratorRootNamespace::class)]
        private string|null $rootNamespace,
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
