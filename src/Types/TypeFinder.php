<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

use Medas\Core\{
    Attributes\Service,
    Interfaces\HasId,
    Interfaces\ManagedCollection,
    Interfaces\Type,
    Interfaces\Uuid as UuidProperty,
    Types\Boolean,
    Types\Collection,
    Types\DateTime,
    Types\FloatingPoint,
    Types\Integer,
    Types\Relation,
    Types\Text,
    Types\Uuid
};
use Medas\EntityManager\{
    Attributes\Entity,
    Attributes\EntityCollection,
    Exceptions\ClassPropertyIsNotARelation,
    Exceptions\EntityCollectionDoesNotImplementManagedCollection,
    Exceptions\PropertyHasMultipleImplicitTypes,
    Exceptions\PropertyHasNoImplicitType,
    Hydration\PropertyTypeNormalizer,
    Properties\PropertyManager
};

#[Service]
readonly class TypeFinder
{
    public function __construct(
        private PropertyTypeNormalizer $normalizer,
        private PropertyManager        $propertyManager,
    )
    {
    }

    public function find(\ReflectionProperty $property): Type
    {
        return $this->findExplicitType($property) ?: $this->findImplicitType($property);
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
            $baseType->getName() === \DateTime::class => new DateTime(),
            $baseType->getName() === UuidProperty::class => new Uuid(),
            !$baseType->isBuiltin() => $this->findRelationType($property, $baseType),
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

    private function findRelationType(\ReflectionProperty $property, \ReflectionNamedType $baseType): Type
    {
        $relationName = $baseType->getName();

        if (enum_exists($relationName)) {
            return new Relation($relationName);
        }

        $class = new \ReflectionClass($relationName);

        if ($collection = attribute(EntityCollection::class, $class)) {
            if (!$class->implementsInterface(ManagedCollection::class)) {
                throw new EntityCollectionDoesNotImplementManagedCollection($class);
            }

            return new Collection($baseType->getName(), $collection->contentType);
        }

        if (attribute(Entity::class, $class)) {
            return new Relation($relationName);
        }

        if ($class->implementsInterface(HasId::class)) {
            return new Relation($relationName);
        }

        throw new ClassPropertyIsNotARelation($property);
    }
}
