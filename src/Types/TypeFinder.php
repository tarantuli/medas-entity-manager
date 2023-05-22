<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\{Guid as GuidProperty, ManagedCollection, Type};
use Medas\EntityManager\Attributes\EntityCollection;
use Medas\EntityManager\Exceptions\EntityCollectionDoesNotImplementManagedCollection;
use Medas\EntityManager\Exceptions\PropertyHasMultipleImplicitTypes;
use Medas\EntityManager\Exceptions\PropertyHasNoImplicitType;
use Medas\EntityManager\Hydration\PropertyTypeNormalizer;
use Medas\EntityManager\Properties\PropertyManager;

#[Service]
class TypeFinder
{
    public function __construct(
        private readonly PropertyTypeNormalizer $normalizer,
        private readonly PropertyManager        $propertyManager,
    )
    {
    }

    public function find(\ReflectionProperty $property): Type
    {
        return $this->findExplicitType($property)
            ?: $this->findImplicitType($property);
    }

    private function findExplicitType(\ReflectionProperty $property): Type|null
    {
        if ($handler = $this->propertyManager->getHandler($property)) {
            return $handler->type();
        }

        return attribute(Type::class, $property);
    }

    private function findImplicitType(\ReflectionProperty $property): Type
    {
        $baseType = $this->getBaseType($property);

        return match (true) {
            $baseType->getName() === 'DateTime' => new DateTime(),
            $baseType->getName() === GuidProperty::class => new Guid(),
            !$baseType->isBuiltin() => $this->findRelationType($baseType),
            $baseType->getName() === 'int' => new Integer(),
            $baseType->getName() === 'float' => new FloatingPoint(),
            $baseType->getName() === 'string' => new Text(),
            $baseType->getName() === 'bool' => new Boolean(),
            default => throw new PropertyHasNoImplicitType($property),
        };
    }

    private function getBaseType(\ReflectionProperty $property): \ReflectionNamedType
    {
        $phpTypes = $this->normalizer->namedTypes($property);

        if ($phpTypes === []) {
            throw new PropertyHasNoImplicitType($property);
        }

        $baseType = null;

        foreach ($phpTypes as $phpType) {
            if ($baseType !== null) {
                throw new PropertyHasMultipleImplicitTypes($property);
            }

            $baseType = $phpType;
        }

        return $baseType;
    }

    private function findRelationType(\ReflectionNamedType $baseType): Type
    {
        $relationName = $baseType->getName();

        $class = new \ReflectionClass($relationName);

        if ($collection = attribute(EntityCollection::class, $class)) {
            if (!$class->implementsInterface(ManagedCollection::class)) {
                throw new EntityCollectionDoesNotImplementManagedCollection($class);
            }

            return new Collection($baseType->getName(), $collection->contentType);
        }

        return new Relation($relationName);
    }
}
