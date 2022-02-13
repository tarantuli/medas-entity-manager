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
            $this->set($metaData, $entity, $propertyName, $value);
        }
    }

    public function set(MetaData $metaData, object $entity, string $propertyName, mixed $value): void
    {
        // If value is null, don't return, but set the value to null

        $property = $metaData->property($propertyName);
        $valueType = get_debug_type($value);

        if (!$property->allowsPhpType($valueType)) {
            foreach ($property->phpTypes as $phpType) {
                if (class_exists($phpType)) {
                    $value = em()->get($phpType, $value);
                    $valueType = get_debug_type($value);
                    break;
                }

                if ($phpType === 'int' && preg_match('/^\d+$/', $value)) {
                    $value = (int) $value;
                    $valueType = 'int';
                }
            }
        }

        if (!$property->allowsPhpType($valueType)) {
            throw new InvalidPropertyTypeException(
                $metaData->className, $propertyName,
                $valueType, $property->phpTypes
            );
        }

        $property->reflection->setValue($entity, $value);
    }
}
