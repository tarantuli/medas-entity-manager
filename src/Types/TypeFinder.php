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
        private PropertyTypeNormalizer $normalizer,
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
        $typeAttribute = $property->getAttributes(
            Type::class,
            \ReflectionAttribute::IS_INSTANCEOF
        );

        if (!$typeAttribute) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $typeAttribute[0]->newInstance();
    }

    private function findImplicitType(\ReflectionProperty $property): Type
    {
        $baseType = $this->getBaseType($property);

        return match (true) {
            !$baseType->isBuiltin() => new Relation($baseType->getName()),
            $baseType->getName() === 'int' => new Integer(),
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
