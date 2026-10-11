<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{
    EntityManager,
    Exceptions\ClassDoesNotExist,
    Exceptions\UnknownProperties,
    Hydration\Hydrator,
    Hydration\ValueCaster,
    Hydration\ValueSetter,
    MetaData,
    MetaDataManager
};

#[Service]
readonly class Initializer
{
    public function __construct(
        private Hydrator        $hydrator,
        private MetaDataManager $metaDataManager,
        private ValueCaster     $valueCaster,
        private ValueSetter     $valueSetter,
    )
    {
    }

    public function initialize(string $className, array $values = [], MetaData|null $metaData = null): object
    {
        // Validate entity class
        if (!class_exists($className)) {
            throw new ClassDoesNotExist($className);
        }

        $metaData ??= $this->metaDataManager->get($className);

        // Validate all values are for known properties
        $unknownProperties = array_diff(
            array_keys($values),
            array_column($metaData->properties, 'name')
        );

        if ($unknownProperties) {
            throw new UnknownProperties($className, $unknownProperties);
        }

        $entity = new $className();

        if ($values) {
            // resetHistory: false is safe because initializeAndHydrate only ever passes the entity ID to initialize(),
            // never collection values. The hydrator establishes the collection baseline via setData(),
            // which is independent of this path.
            $this->valueSetter->setValues($metaData, $entity, $values);
        }

        return $entity;
    }

    public function initializeAndHydrate(string $className, mixed $id, EntityManager $entityManager): object
    {
        $metaData = $this->metaDataManager->get($className);

        if ($metaData->inheritance->storeOriginalClass && $className === $metaData->inheritance->sharedParentClass) {
            $className = $this->hydrator->fetchOriginalClass($metaData, $id);
        }

        $entity = $this->initialize($className, [$metaData->idProperty->name => $id], $metaData);

        $this->hydrator->hydrate($metaData, $entity, $entityManager);

        return $entity;
    }

    /**
     * Returns a lazy ghost of the entity with only its id set, which is hydrated on first access to any other
     * property - exactly as initializeAndHydrate() would, but on the ghost. $afterHydration is called with the
     * entity once it's hydrated.
     *
     * Returns null when the entity can't be lazy: when its actual class has to be read from storage first
     * (inheritance with a stored original class), or when PHP can't make the class lazy.
     */
    public function initializeLazily(
        string        $className,
        mixed         $id,
        EntityManager $entityManager,
        \Closure      $afterHydration,
    ): object|null
    {
        $metaData = $this->metaDataManager->get($className);

        if ($metaData->inheritance->storeOriginalClass && $className === $metaData->inheritance->sharedParentClass) {
            return null;
        }

        $class = new \ReflectionClass($className);

        // Abstract classes and classes extending internal PHP classes can't be lazy
        if ($class->isAbstract() || $class->isInternal() || $this->extendsInternalClass($class)) {
            return null;
        }

        $entity = $class->newLazyGhost(
            function (object $entity) use ($metaData, $id, $entityManager, $afterHydration)
            : void {
            // The same as initialize(): constructor first, so it can't overwrite the id or hydrated values
            if (method_exists($entity, '__construct')) {
                $entity->__construct();
            }

            $this->valueSetter->setValues($metaData, $entity, [$metaData->idProperty->name => $id]);
            $this->hydrator->hydrate($metaData, $entity, $entityManager);

            $afterHydration($entity);
        });

        // Set the id without loading the entity, so reading the id of a relation costs no query
        $metaData->idProperty->reflection->setRawValueWithoutLazyInitialization(
            $entity,
            $this->valueCaster->cast($metaData->idProperty, $id)
        );

        return $entity;
    }

    private function extendsInternalClass(\ReflectionClass $class): bool
    {
        while ($class = $class->getParentClass()) {
            if ($class->isInternal()) {
                return true;
            }
        }

        return false;
    }
}
