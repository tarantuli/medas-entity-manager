<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\EntityManager\Exceptions\InvalidPropertyType;
use Medas\EntityManager\MetaData;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Values\Interfaces\{Guid, GuidProvider};

#[Service]
class ValueSetter
{
    public function __construct(
        private readonly GuidProvider|null $guidProvider,
    )
    {
    }

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

        if (!$property->allowsPhpType($valueType) && $value !== null) {
            foreach ($property->phpTypes as $phpType) {
                if ($phpType === \DateTime::class) {
                    $value = new \DateTime($value);
                    $valueType = $phpType;
                    break;
                }

                if ($phpType === Guid::class && is_string($value)) {
                    $value = $this->guidProvider->fromString($value);
                }

                if (class_exists($phpType)) {
                    $value = em()->get($phpType, $value);
                    $valueType = $phpType;
                    break;
                }

                if (interface_exists($phpType) && $value instanceof $phpType) {
                    $valueType = $phpType;
                    break;
                }

                if ($phpType === 'int' && preg_match('/^\d+$/', $value)) {
                    $value = (int) $value;
                    $valueType = 'int';
                    break;
                }

                if ($phpType === 'bool' && is_int($value)) {
                    $value = (bool) $value;
                    $valueType = 'bool';
                    break;
                }
            }
        }

        if (!$property->allowsPhpType($valueType)) {
            throw new InvalidPropertyType(
                $metaData->className, $propertyName,
                $valueType, $property->phpTypes
            );
        }

        $property->reflection->setValue($entity, $value);
    }
}
