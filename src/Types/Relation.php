<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

use Medas\EntityManager\Attributes\HasId;
use Medas\EntityManager\Exceptions\ValueDoesNotImplementHasIdException;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Relation extends BaseType
{
    public function __construct(
        public string $entity
    )
    {
    }

    public function serialize(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof HasId) {
            throw new ValueDoesNotImplementHasIdException($value);
        }

        return $value->id();
    }
}
