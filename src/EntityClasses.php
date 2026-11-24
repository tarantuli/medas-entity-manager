<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{
    Attributes\ConfigValue,
    Attributes\Service,
    CachedImplementorList,
    Interfaces\FileLoader,
    Interfaces\PackageEntities
};

/**
 * Lists the entity classes: the classes marked with `#[Entity]` in the configured entity directories, and in the
 * directories that packages publish through @class(Medas\Core\Interfaces\PackageEntities).
 *
 * @sideEffect Loads every PHP file in those directories the first time the list is built.
 * @cache The list is cached in the `'default'` cache, so new entity classes only appear after the caches are cleared.
 */
#[Service]
readonly class EntityClasses
{
    private const string CACHE_KEY = 'Medas\EntityManager\EntityClasses::get';

    private CachedImplementorList $packageEntities;

    public function __construct(
        private FileLoader $fileLoader,

        #[ConfigValue(ConfigOptions\EntityDirectories::class)]
        private array      $entityDirectories,
    )
    {
        $this->packageEntities = new CachedImplementorList(PackageEntities::class);
    }

    /** @return string[] */
    public function get(): array
    {
        return cache(self::CACHE_KEY, fn() => $this->fetchAll());
    }

    /** @return string[] */
    private function fetchAll(): array
    {
        $directories = $this->directories();

        $this->loadClassesInEntityDirectories($directories);

        return $this->processLoadedClasses($directories);
    }

    /**
     * The configured entity directories, followed by those of the packages with entities of their own
     *
     * @return string[]
     */
    private function directories(): array
    {
        $directories = $this->entityDirectories;

        foreach ($this->packageEntities->get() as $packageEntities) {
            /** @var PackageEntities $packageEntities */
            $directories = [...$directories, ...$packageEntities->directories()];
        }

        return array_values(array_unique($directories));
    }

    /** @param string[] $directories */
    private function loadClassesInEntityDirectories(array $directories): void
    {
        foreach ($directories as $directory) {
            $this->fileLoader->load(realpath($directory));
        }
    }

    /**
     * @param string[] $directories
     *
     * @return string[]
     */
    private function processLoadedClasses(array $directories): array
    {
        $entities = [];

        foreach (get_declared_classes() as $className) {
            if ($this->isEntityClass($className, $directories)) {
                $entities[] = $className;
            }
        }

        return $entities;
    }

    /** @param string[] $directories */
    private function isEntityClass(string $className, array $directories): bool
    {
        $reflectionClass = new \ReflectionClass($className);

        $hasEntityAttribute = $reflectionClass->getAttributes(
            Attributes\Entity::class,
            \ReflectionAttribute::IS_INSTANCEOF
        );

        if (!$hasEntityAttribute) {
            return false;
        }

        return array_any(
            $directories,
            fn($directory)
                => str_starts_with(
                    $reflectionClass->getFileName(),
                    realpath($directory) . DIRECTORY_SEPARATOR
                )
        );
    }
}
