<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\Core\{
    Attributes\ConfigValue,
    Attributes\Service,
    Collections\GenericCollection,
    ConfigOptions\DispatchDebugInformation,
    Events\DebugInformation,
    Interfaces\ManagedCollection,
    Interfaces\SettableCollection,
    Interfaces\TracksChanges
};
use Medas\EntityManager\MetaData;

#[Service]
readonly class ValueSetter
{
    public function __construct(
        private ValueCaster $valueCaster,

        #[ConfigValue(DispatchDebugInformation::class)]
        private bool        $dispatchDebugInformation = false,
    )
    {
    }

    public function setValues(
        MetaData $metaData,
        object   $entity,
        array    $values,
        bool     $ignoreUnknownProperties = false,
        bool     $resetHistory = false,
    ): void
    {
        foreach ($values as $propertyName => $value) {
            $this->set(
                $metaData,
                $entity,
                $propertyName,
                $value,
                $ignoreUnknownProperties,
                $resetHistory
            );
        }
    }

    public function set(
        MetaData $metaData,
        object   $entity,
        string   $propertyName,
        mixed    $value,
        bool     $ignoreUnknownProperties = false,
        bool     $resetHistory = false,
    ): void
    {
        $this->dispatchDebugInformation
            && dispatch(new DebugInformation(
                '[value-setter] %s[%s] %s => %s',
                $entity::class,
                spl_object_id($entity),
                $propertyName,
                $value
            ));

        // If value is null, don't return, but set the value to null
        if (null === $property = $metaData->property($propertyName, $ignoreUnknownProperties)) {
            return;
        }

        $value = $this->valueCaster->cast($property, $value);

        if (isset($entity->{$propertyName}) && $entity->{$propertyName} instanceof SettableCollection) {
            /** @var SettableCollection $reference */
            $reference = &$entity->{$propertyName};

            $reference->setData($value instanceof GenericCollection ? $value->__serialize() : $value);
        }
        elseif (isset($entity->{$propertyName}) && $entity->{$propertyName} instanceof ManagedCollection) {
            /** @var ManagedCollection $reference */
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

        if ($value instanceof TracksChanges && $resetHistory) {
            $value->resetChangeTracking();
        }
    }
}
