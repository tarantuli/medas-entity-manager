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

    /**
     * Applies a payload through the entity's writable contract, keyed by each
     * field's source. Distinct from setValues() (raw property hydration, e.g.
     * loading from storage): here a field's source may be a method or route
     * through a setter, so it can't just be assigned. Values are expected
     * pre-filtered by the normalizer to the fields allowed for the operation.
     */
    public function applyWritable(MetaData $metaData, object $entity, array $values): void
    {
        foreach ($metaData->writableFields as $field) {
            if (!array_key_exists($field->source, $values)) {
                continue;
            }

            $value = $values[$field->source];

            if ($field->isMethod) {
                $entity->{$field->source}($value);
            }
            elseif ($field->setter !== null) {
                $entity->{$field->setter}($value);
            }
            else {
                // Plain property: reuse set() for its casting and collection
                // handling.
                $this->set($metaData, $entity, $field->source, $value);
            }
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

        if (isset($entity->{$propertyName}) && $entity->{$propertyName} instanceof ManagedCollection) {
            /** @var ManagedCollection $reference */
            $reference = &$entity->{$propertyName};

            foreach ($value as $subValue) {
                if (!$reference->contains($subValue)) {
                    $reference[] = $subValue;
                }
            }

            // Remove anything the existing collection has that the new
            // value doesn't - without this, removing an item (down to and
            // including emptying the collection entirely) never had any
            // effect, since the loop above only ever adds. Collected into
            // a separate array first and removed in a second pass, rather
            // than unset() while iterating $reference directly - mutating
            // an array during iteration via its internal pointer is not
            // safe to rely on.
            $keysToRemove = [];

            foreach ($reference as $key => $existingValue) {
                if (!$value->contains($existingValue)) {
                    $keysToRemove[] = $key;
                }
            }

            foreach ($keysToRemove as $key) {
                unset($reference[$key]);
            }
        }
        elseif (isset($entity->{$propertyName}) && $entity->{$propertyName} instanceof SettableCollection) {
            /** @var SettableCollection $reference */
            $reference = &$entity->{$propertyName};

            $reference->setData($value instanceof GenericCollection ? $value->__serialize() : $value);
        }
        else {
            $property->reflection->setValue($entity, $value);
        }

        if ($value instanceof TracksChanges && $resetHistory) {
            $value->resetChangeTracking();
        }
    }
}
