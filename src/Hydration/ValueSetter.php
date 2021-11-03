<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\EntityManager\Exceptions\InvalidPropertyTypeException;
use Medas\EntityManager\MetaData;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ValueSetter
{
    public function setValues(MetaData $metaData, object $entity, array $values): void
    {
        foreach ($values as $propertyName => $value) {
            $this->setValue($metaData, $entity, $propertyName, $value);
        }
    }

    public function setValue(MetaData $metaData, object $entity, string $propertyName, mixed $value): void
    {
        $property = $metaData->getProperty($propertyName);
        $valueType = get_debug_type($value);

        if (!$property->allowsPhpType($valueType)) {
            throw new InvalidPropertyTypeException(
                $metaData->className, $propertyName,
                $valueType, $property->phpTypes
            );
        }

        $property->reflection->setValue($entity, $value);
    }
}
