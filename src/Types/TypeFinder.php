<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

use Medas\EntityManager\Exceptions\PropertyHasMultipleImplicitTypesException;
use Medas\EntityManager\Exceptions\PropertyHasNoImplicitTypeException;
use Medas\EntityManager\Hydration\PropertyTypeNormalizer;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class TypeFinder
{
    public function __construct(
        private readonly PropertyTypeNormalizer $normalizer,
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
        return attribute(Type::class, $property);
    }

    private function findImplicitType(\ReflectionProperty $property): Type
    {
        $baseType = $this->getBaseType($property);

        return match (true) {
            $baseType->getName() === 'DateTime' => new DateTime(),
            !$baseType->isBuiltin() => new Relation($baseType->getName()),
            $baseType->getName() === 'int' => new Integer(),
            $baseType->getName() === 'float' => new FloatingPoint(),
            $baseType->getName() === 'string' => new Text(),
            $baseType->getName() === 'bool' => new Boolean(),
            default => throw new PropertyHasNoImplicitTypeException($property),
        };
    }

    private function getBaseType(\ReflectionProperty $property): \ReflectionNamedType
    {
        $phpTypes = $this->normalizer->namedTypes($property);

        if ($phpTypes === []) {
            throw new PropertyHasNoImplicitTypeException($property);
        }

        $baseType = null;

        foreach ($phpTypes as $phpType) {
            if ($baseType !== null) {
                throw new PropertyHasMultipleImplicitTypesException($property);
            }

            $baseType = $phpType;
        }

        return $baseType;
    }
}
