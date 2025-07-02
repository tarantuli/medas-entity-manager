<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\Core\{Attributes\Service, Interfaces\HasId, Interfaces\Uuid};
use Medas\EntityManager\MetaData\Property;

#[Service]
readonly class ValueComparer
{
    public function __construct(
        private ValueGetter $getter,
    )
    {
    }

    public function hasValue(Property $property, object $entity, mixed $value): bool
    {
        $entityValue = $this->getter->getValue($entity, $property);

        $this->normalize($value);
        $this->normalize($entityValue);

        return $value === $entityValue;
    }

    private function normalize(mixed &$aValue): void
    {
        if ($aValue instanceof HasId) {
            $aValue = $aValue->id();
        }

        // This check MUST be after checking for instances of HasId
        if ($aValue instanceof Uuid) {
            $aValue = $aValue->toBytes();
        }

        if ($aValue instanceof \DateTime) {
            $aValue = $aValue->getTimestamp();
        }
    }
}
