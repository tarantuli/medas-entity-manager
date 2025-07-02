<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\Core\{
    Attributes\Service,
    Interfaces\Collection,
    Interfaces\TracksChanges,
    Interfaces\Uuid,
    Interfaces\UuidProvider
};
use Medas\EntityManager\{
    Attributes\EntityCollection,
    Entities\Initializer,
    Exceptions\InvalidPropertyType,
    MetaData,
    MetaData\Property
};

#[Service]
readonly class ValueCaster
{
    public function __construct(
        private UuidProvider|null $uuidProvider,
    )
    {
    }

    public function cast(MetaData $metaData, Property $property, mixed $value): mixed
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
                    $value = $this->uuidProvider->fromString($value);
                }

                if (enum_exists($phpType)) {
                    $backingType = (new \ReflectionEnum($phpType))->getBackingType();

                    if (((string) $backingType === 'int') && is_string($value)) {
                        $value = intval($value);
                    }

                    $value = $phpType::from($value);
                    $valueType = $phpType;

                    break;
                }

                if (class_exists($phpType)) {
                    if ($attribute = attribute(EntityCollection::class, new \ReflectionClass($phpType))) {
                        // We cannot inject the Initializer in the constructor because that already depends on this class
                        $initializer = service(Initializer::class);
                        $collection = new $phpType();
                        $itemType = $attribute->contentType;

                        foreach ($value as $itemValues) {
                            $collection[] = $initializer->initialize($itemType, $itemValues);
                        }

                        if ($collection instanceof TracksChanges) {
                            $collection->resetChangeTracking();
                        }

                        $value = $collection;
                    }
                    elseif (!$value instanceof Collection) {
                        $value = em()->get($phpType, $value);
                    }

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

                if ($phpType === 'float' && is_int($value)) {
                    $value = (float) $value;
                    $valueType = 'float';

                    break;
                }
            }
        }

        if (!$property->allowsPhpType($valueType)) {
            throw new InvalidPropertyType(
                $metaData->className,
                $property->name,
                $valueType,
                $property->phpTypes
            );
        }

        return $value;
    }
}
