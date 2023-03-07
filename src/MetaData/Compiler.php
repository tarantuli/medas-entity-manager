<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

use Medas\EntityManager\{Attributes,
    Exceptions\ClassIsNotAnEntity,
    Exceptions\EntityHasMultipleIdProperties,
    Exceptions\EntityHasNoIdProperty,
    Hydration\PropertyTypeNormalizer,
    MetaData,
    Types\TypeFinder
};
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Compiler
{
    public function __construct(
        private readonly PropertyTypeNormalizer $propertyTypeNormalizer,
        private readonly TypeFinder             $typeFinder,
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

        if (!$entity = attribute(Attributes\Entity::class, $class)) {
            throw new ClassIsNotAnEntity($className);
        }

        $metaData = new MetaData($className);
        $metaData->entity = $entity;
        $metaData->sourceFileDate = filemtime($class->getFileName());
        $metaData->properties = [];
        $metaData->references = [];

        $this->processProperties($class, $metaData);
        $this->findIdProperty($metaData);

        return $metaData;
    }

    private function processProperties(\ReflectionClass $class, MetaData $metaData): void
    {
        foreach ($class->getProperties() as $property) {
            $this->processProperty($property, $metaData);
            $this->processReferences($property, $metaData);
        }
    }

    private function processProperty(\ReflectionProperty $property, MetaData $metaData): void
    {
        $isManagedProperty = $property->getAttributes(
            Attributes\Property::class,
            \ReflectionAttribute::IS_INSTANCEOF
        );

        if (!$isManagedProperty) {
            return;
        }

        $type = $this->typeFinder->find($property);
        $isNullable = $property->getType()->allowsNull();

        $metaData->properties[] = new Property(
            name: $property->name,
            type: $type,
            hasDefault: $property->hasDefaultValue(),
            default: $property->hasDefaultValue() ? $property->getDefaultValue() : null,
            isId: !empty($property->getAttributes(Attributes\Id::class, \ReflectionAttribute::IS_INSTANCEOF)),
            isGeneratedValue: !empty($property->getAttributes(Attributes\IsGeneratedValue::class)),
            isCreationTimestamp: !empty($property->getAttributes(Attributes\IsCreationTimestamp::class)),
            isModificationTimestamp: !empty($property->getAttributes(Attributes\IsModificationTimestmap::class)),
            isNullable: $isNullable,
            isUnique: !empty($property->getAttributes(Attributes\IsUnique::class)),
            onDeleteCascade: !empty($property->getAttributes(Attributes\OnDeleteCascade::class)),
            phpTypes: $this->propertyTypeNormalizer->names($property),
            reflection: $property
        );
    }

    private function processReferences(\ReflectionProperty $property, MetaData $metaData): void
    {
        $references = attribute(Attributes\References::class, $property);

        if (!$references) {
            return;
        }

        if ($references->property === null) {
            $targetClass = new \ReflectionClass($references->entity);

            $targetProperties = [];

            foreach ($targetClass->getProperties() as $targetProperty) {
                if (in_array($metaData->className, $this->propertyTypeNormalizer->names($targetProperty))) {
                    $targetProperties[] = $targetProperty;
                }
            }

            if (count($targetProperties) === 0) {
                throw new \Exception('property ' . $property->name . ' should be referenced by ' . $references->entity . ' but no properties refer to ' . $metaData->className);
            }

            if (count($targetProperties) >= 2) {
                throw new \Exception('property ' . $property->name . ' should be referenced by ' . $references->entity . ' but too many properties refer to ' . $metaData->className);
            }

            $references->property = $targetProperties[0]->name;
        }

        $metaData->references[] = new Reference(
            name: $property->name,
            entity: $references->entity,
            property: $references->property,
        );
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
}
