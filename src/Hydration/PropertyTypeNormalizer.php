<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class PropertyTypeNormalizer
{
    /**@return string[] */
    public function getNames(\ReflectionProperty $property): array
    {
        return array_map(fn($value): string => $value->getName(), $this->getNamedTypes($property));
    }

    /**@return \ReflectionNamedType[] */
    public function getNamedTypes(\ReflectionProperty $property): array
    {
        $type = $property->getType();

        if (null === $type) {
            return [];
        }

        return $type instanceof \ReflectionUnionType ? $type->getTypes() : [$type];
    }

}
