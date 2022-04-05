<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

use Medas\EntityManager\{Attributes,
    Attributes\Entity,
    Attributes\Id,
    Attributes\IsNullable,
    Attributes\IsUnique,
    Exceptions\ClassIsNotAnEntityException,
    Exceptions\EntityHasNoIdPropertyException,
    Hydration\PropertyTypeNormalizer,
    MetaData,
    Types\TypeFinder
};
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Compiler
{
    public function __construct(
        private PropertyTypeNormalizer $propertyTypeNormalizer,
        private TypeFinder             $typeFinder,
    )
    {
    }

    public function compile(string $className): MetaData
    {
        $class = new \ReflectionClass($className);

        if (!$entity = attribute(Entity::class, $class)) {
            throw new ClassIsNotAnEntityException($className);
        }

        $metaData = new MetaData($className);
        $metaData->entity = $entity;
        $metaData->sourceFileDate = filemtime($class->getFileName());

        $this->determineProperties($class, $metaData);
        $this->determineIdProperties($metaData);

        return $metaData;
    }

    private function determineProperties(\ReflectionClass $class, MetaData $metaData)
    {
        foreach ($class->getProperties() as $property) {
            $this->processProperty($property, $metaData);
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

        $metaData->properties[] = new Property(
            name: $property->name,
            type: $type,
            default: $property->hasDefaultValue() ? $property->getDefaultValue() : null,
            isId: !empty($property->getAttributes(Id::class)),
            isGeneratedValue: !empty($property->getAttributes(Attributes\IsGeneratedValue::class)),
            isNullable: !empty($property->getAttributes(IsNullable::class)),
            isUnique: !empty($property->getAttributes(IsUnique::class)),
            phpTypes: $this->propertyTypeNormalizer->names($property),
            reflection: $property
        );
    }

    private function determineIdProperties(MetaData $metaData): void
    {
        $metaData->idProperties = [];
        $metaData->hasCompositeId = false;

        foreach ($metaData->properties as $property) {
            if ($property->isId) {
                $metaData->idProperties[] = $property;
                $metaData->idProperty = $property;
            }
        }

        if (count($metaData->idProperties) === 0) {
            throw new EntityHasNoIdPropertyException($metaData->className);
        }

        if (count($metaData->idProperties) > 1) {
            $metaData->idProperty = null;
            $metaData->hasCompositeId = true;
        }
    }
}
