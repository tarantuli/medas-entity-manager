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

    public function isEqual(Property $property, object $entity, mixed $value): bool
    {
        $entityValue = $this->getter->getValue($entity, $property);

        foreach ([$value, $entityValue] as &$aValue) {
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

        return $value === $entityValue;
    }
}
