<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\Core\{Attributes\Service, Interfaces\ManagedCollection};
use Medas\EntityManager\MetaData;

#[Service]
readonly class ValueSetter
{
    public function __construct(
        private ValueCaster $valueCaster,
    )
    {
    }

    public function setValues(
        MetaData $metaData,
        object   $entity,
        array    $values,
        bool     $ignoreUnknownProperties = false
    ): void
    {
        foreach ($values as $propertyName => $value) {
            $this->set($metaData, $entity, $propertyName, $value, $ignoreUnknownProperties);
        }
    }

    public function set(
        MetaData $metaData,
        object   $entity,
        string   $propertyName,
        mixed    $value,
        bool     $ignoreUnknownProperties = false
    ): void
    {
        // If value is null, don't return, but set the value to null
        if (null === $property = $metaData->property($propertyName, $ignoreUnknownProperties)) {
            return;
        }

        $value = $this->valueCaster->cast($property, $value);

        if (isset($entity->{$propertyName}) && $entity->{$propertyName} instanceof ManagedCollection) {
            $reference = &$entity->{$propertyName};

            foreach ($value as $subValue) {
                if (!$reference->contains($subValue)) {
                    $reference[] = $subValue;
                }
            }
        }
        else {
            $property->reflection->setValue($entity, $value);
        }
    }
}
