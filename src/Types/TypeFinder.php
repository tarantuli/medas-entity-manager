<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

use Medas\EntityManager\Exceptions\PropertyHasMultipleImplicitTypes;
use Medas\EntityManager\Exceptions\PropertyHasNoImplicitType;
use Medas\EntityManager\Hydration\PropertyTypeNormalizer;
use Medas\EntityManager\Properties\PropertyManager;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Interfaces\{Guid as GuidProperty, Type};

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
            !$baseType->isBuiltin() => new Relation($baseType->getName()),
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
}
