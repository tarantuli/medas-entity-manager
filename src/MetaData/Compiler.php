<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{
    Attributes,
    Exceptions\ClassIsNotAnEntity,
    Exceptions\EntityHasMultipleIdProperties,
    Exceptions\EntityHasNoIdProperty,
    Interfaces\HasSoftDeletes,
    MetaData
};

#[Service]
readonly class Compiler
{
    public function __construct(
        private Compiler\PropertyProcessor $propertyProcessor,
        private EntityCompiler             $entityCompiler,
    )
    {
    }

    public function compile(string $className): MetaData
    {
        try {
            $class = new \ReflectionClass($className);
        }
        catch (\ReflectionException) {
            throw new ClassIsNotAnEntity($className);
        }

        if (!$entity = $this->entityCompiler->compile($class)) {
            throw new ClassIsNotAnEntity($className);
        }

        $metaData = new MetaData($className);

        $metaData->entity = $entity;
        $metaData->sourceFileDate = filemtime($class->getFileName());
        $metaData->properties = [];
        $metaData->backReferences = [];
        $metaData->inheritance = new Inheritance($class->getParentClass() ? $class->getParentClass()->name : null);

        $this->propertyProcessor->processProperties($class, $metaData);
        $this->findIdProperty($metaData);
        $this->checkForStoreOriginalEntityType($metaData, $class);
        $this->checkForUniquePropertySets($metaData, $class);
        $this->checkForCompoudIndexes($metaData, $class);
        $this->checkForOwnershipFilters($metaData, $class);
        $this->checkForSoftDeletes($metaData, $class);

        return $metaData;
    }

    private function findIdProperty(MetaData $metaData): void
    {
        $foundProperty = false;

        foreach ($metaData->properties as $property) {
            if ($property->isId) {
                if ($foundProperty) {
                    throw new EntityHasMultipleIdProperties($metaData->className);
                }

                $metaData->idProperty = $property;
                $foundProperty = true;
            }
        }

        if (!$foundProperty) {
            throw new EntityHasNoIdProperty($metaData->className);
        }
    }

    private function checkForStoreOriginalEntityType(MetaData $metaData, \ReflectionClass $classToCheck): void
    {
        if ($classToCheck->getAttributes(Attributes\StoreOriginalEntityType::class)) {
            $metaData->inheritance->storeOriginalClass = true;
            $metaData->inheritance->sharedParentClass = $classToCheck->name;

            return;
        }

        if ($parentClass = $classToCheck->getParentClass()) {
            $this->checkForStoreOriginalEntityType($metaData, $parentClass);
        }
    }

    private function checkForUniquePropertySets(MetaData $metaData, \ReflectionClass $class): void
    {
        $metaData->uniquePropertySets = [];

        foreach ($class->getAttributes(Attributes\UniquePropertySet::class) as $attribute) {
            /** @var Attributes\UniquePropertySet $instance */
            $instance = $attribute->newInstance();
            $metaData->uniquePropertySets[] = $instance->properties;
        }
    }

    private function checkForCompoudIndexes(MetaData $metaData, \ReflectionClass $class): void
    {
        $metaData->compoundIndexes = [];

        foreach ($class->getAttributes(Attributes\CompoundIndex::class) as $attribute) {
            /** @var Attributes\CompoundIndex $instance */
            $instance = $attribute->newInstance();
            $metaData->compoundIndexes[] = $instance->properties;
        }
    }

    private function checkForOwnershipFilters(MetaData $metaData, \ReflectionClass $class): void
    {
        if ($attribute = attribute(Attributes\AddOwnershipFilter::class, $class)) {
            $metaData->ownershipFilters = $attribute->filters;
        }
        else {
            $metaData->ownershipFilters = [];
        }
    }

    private function checkForSoftDeletes(MetaData $metaData, \ReflectionClass $class): void
    {
        $metaData->softDeletes = $class->implementsInterface(HasSoftDeletes::class);
    }
}
