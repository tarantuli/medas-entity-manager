<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{
    Attributes\ConfigValue,
    Attributes\Service,
    Interfaces\CacheManager,
    Interfaces\DirectoryManager
};

#[Service]
readonly class EntityClasses
{
    private const CACHE_KEY = 'Medas\EntityManager\EntityClasses::get';

    public function __construct(
        private CacheManager     $cacheManager,
        private DirectoryManager $directoryManager,

        #[ConfigValue(ConfigOptions\EntityDirectories::class)]
        private array            $entityDirectories,
    )
    {
    }

    public function get(): array
    {
        return $this->cacheManager->get()->get(self::CACHE_KEY, fn() => $this->fetchAll());
    }

    private function fetchAll(): array
    {
        $this->loadClassesInEntityDirectories();

        return $this->processLoadedClasses();
    }

    private function loadClassesInEntityDirectories(): void
    {
        foreach ($this->entityDirectories as $directory) {
            $this->directoryManager->loadPhpFiles(realpath($directory));
        }
    }

    private function processLoadedClasses(): array
    {
        $entities = [];

        foreach (get_declared_classes() as $className) {
            if ($this->isEntityClass($className)) {
                $entities[] = $className;
            }
        }

        return $entities;
    }

    private function isEntityClass(string $className): bool
    {
        $reflectionClass = new \ReflectionClass($className);

        $hasEntityAttribute = $reflectionClass->getAttributes(
            Attributes\Entity::class,
            \ReflectionAttribute::IS_INSTANCEOF
        );

        if (!$hasEntityAttribute) {
            return false;
        }

        foreach ($this->entityDirectories as $directory) {
            if (str_starts_with($reflectionClass->getFileName(), realpath($directory) . DIRECTORY_SEPARATOR)) {
                return true;
            }
        }

        return false;
    }
}
