<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\MetaData\Property;

#[Service]
readonly class ObjectValues
{
    public function __construct(
        private ValueCaster   $caster,
        private ValueComparer $comparer,
    )
    {
    }

    public function cast(Property $property, mixed $value): mixed
    {
        return $this->caster->cast($property, $value);
    }

    public function is(Property $property, object $entity, mixed $value): bool
    {
        $value = $this->cast($property, $value);

        return $this->comparer->hasValue($property, $entity, $value);
    }

    public function get(object $entity, Property $field): mixed
    {
        return $field->reflection->getValue($entity);
    }

    public function set(object $entity, Property $property, mixed $value): void
    {
        $value = $this->cast($property, $value);

        $property->reflection->setValue($entity, $value);
    }
}
