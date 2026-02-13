<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\Core\{Attributes\Service, Interfaces\HasId, Interfaces\PropertyHandler, Interfaces\Uuid};
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

        $this->normalize($property, $value);
        $this->normalize($property, $entityValue);

        return $value === $entityValue;
    }

    private function normalize(Property $property, mixed &$value): void
    {
        if ($property->handler) {
            /** @var PropertyHandler $handler */
            $handler = \service($property->handler);
            $value = $handler->serialize($value);
        }

        if ($value instanceof HasId) {
            $value = $value->id();
        }

        // This check MUST be after checking for instances of HasId
        if ($value instanceof Uuid) {
            $value = $value->toBytes();
        }

        if ($value instanceof \DateTime) {
            $value = $value->getTimestamp();
        }
    }
}
