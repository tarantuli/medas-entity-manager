<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Attributes;
use Medas\EntityManager\Exceptions\{
    ClassIsNotAnEntity,
    EntityHasMultipleIdProperties,
    EntityHasNoIdProperty
};
use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaData\Compiler\PropertyProcessor;

#[Service]
readonly class Compiler
{
    public function __construct(
        private EntityCompiler    $entityCompiler,
        private PropertyProcessor $propertyProcessor,
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
        $metaData->references = [];
        $metaData->inheritance = new Inheritance($class->getParentClass() ? $class->getParentClass()->name : null);

        $this->propertyProcessor->processProperties($class, $metaData);
        $this->findIdProperty($metaData);
        $this->checkForStoreOriginalEntityType($metaData, $class);

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
}
