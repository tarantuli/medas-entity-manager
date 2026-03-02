<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\Core\{
    Attributes\DataHolder,
    Attributes\Service,
    Interfaces\Collection,
    Interfaces\TracksChanges,
    Interfaces\Uuid,
    Interfaces\UuidProvider
};
use Medas\EntityManager\{
    Attributes\EntityCollection,
    Entities\Initializer,
    Events\FindEntity,
    Exceptions\DataLossOnConversion,
    Exceptions\FailedToFindEntity,
    Exceptions\IntegerOverflow,
    Exceptions\InvalidNumericValue,
    Exceptions\InvalidPropertyType,
    Exceptions\InvalidUuidBytes,
    Exceptions\InvalidUuidFormat,
    Exceptions\UnrecognizedUuidFormat,
    MetaData\Property
};
use Medas\ObjectToArraySerializer\ArrayToObjectCaster;

#[Service]
readonly class ValueCaster
{
    public function __construct(
        private ArrayToObjectCaster $arrayToObjectCaster,
        private UuidProvider|null   $uuidProvider,
    )
    {
    }

    public function cast(Property $property, mixed $value): mixed
    {
        $valueType = get_debug_type($value);

        if (!$property->allowsPhpType($valueType) && $value !== null) {
            foreach ($property->phpTypes as $phpType) {
                if ($phpType === \DateTime::class) {
                    $value = new \DateTime($value);
                    $valueType = $phpType;

                    break;
                }

                if ($phpType === Uuid::class && is_string($value)) {
                    // Try UUID string format first (more common)
                    if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
                        try {
                            $value = $this->uuidProvider->fromString($value);
                        }
                        catch (\Exception) {
                            throw new InvalidUuidFormat($property->name, $value);
                        }
                    }

                    // Then try binary format
                    elseif (strlen($value) === 16) {
                        try {
                            $value = $this->uuidProvider->fromBytes($value);
                        }
                        catch (\Exception) {
                            throw new InvalidUuidBytes($property->name, $value);
                        }
                    }
                    else {
                        throw new UnrecognizedUuidFormat($property->name, $value);
                    }
                }

                if (enum_exists($phpType)) {
                    $backingType = new \ReflectionEnum($phpType)->getBackingType();

                    if (((string) $backingType === 'int') && is_string($value)) {
                        $value = intval($value);
                    }

                    $value = $phpType::from($value);
                    $valueType = $phpType;

                    break;
                }

                if (class_exists($phpType)) {
                    $reflectionClass = new \ReflectionClass($phpType);

                    if ($attribute = attribute(EntityCollection::class, $reflectionClass)) {
                        // We cannot inject the Initializer in the constructor because that already depends on this class
                        $initializer = service(Initializer::class);
                        $collection = new $phpType();
                        $itemType = $attribute->contentType;

                        foreach ($value as $itemValues) {
                            if ($itemValues instanceof $itemType) {
                                $collection[] = $itemValues;
                            }
                            else {
                                $collection[] = $initializer->initialize($itemType, $itemValues);
                            }
                        }

                        if ($collection instanceof TracksChanges) {
                            $collection->resetChangeTracking();
                        }

                        $value = $collection;
                    }
                    elseif (attribute(DataHolder::class, $reflectionClass) && is_array($value)) {
                        $value = $this->arrayToObjectCaster->cast($value, $phpType);
                    }
                    elseif (!$value instanceof Collection) {
                        $event = new FindEntity($phpType, $value);

                        dispatch($event);

                        if ($event->entity === null) {
                            throw new FailedToFindEntity($phpType, $value);
                        }

                        $value = $event->entity;
                    }

                    $valueType = $phpType;

                    break;
                }

                if (interface_exists($phpType) && $value instanceof $phpType) {
                    $valueType = $phpType;

                    break;
                }

                if ($phpType === 'int') {
                    if (!is_numeric($value)) {
                        throw new InvalidNumericValue($property->name, $value);
                    }

                    // Check for overflow before casting
                    if ($value > PHP_INT_MAX || $value < PHP_INT_MIN) {
                        throw new IntegerOverflow($property->name, $value);
                    }

                    $intValue = (int) $value;

                    // Verify no data loss
                    if ((string) $intValue !== (string) $value) {
                        throw new DataLossOnConversion($property->name, $value, $intValue);
                    }

                    $value = $intValue;
                    $valueType = 'int';

                    break;
                }

                if ($phpType === 'bool' && is_int($value)) {
                    $value = (bool) $value;
                    $valueType = 'bool';

                    break;
                }

                if ($phpType === 'float' && is_int($value)) {
                    $value = (float) $value;
                    $valueType = 'float';

                    break;
                }
            }
        }

        if (!$property->allowsPhpType($valueType)) {
            throw new InvalidPropertyType(
                $property->reflection->class,
                $property->name,
                $valueType,
                $property->phpTypes
            );
        }

        return $value;
    }
}
