<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\{Attributes\Service, Interfaces\HasId, Interfaces\StringSerializer, Interfaces\Type};
use Medas\EntityManager\Exceptions\CannotCastValueToId;

#[Service]
readonly class IdSerializer implements StringSerializer
{
    public function serialize(mixed $value): string
    {
        if ($value instanceof HasId) {
            $value = $value->id();
        }

        if ($value === null || is_scalar($value) || $value instanceof \Stringable) {
            return (string) $value;
        }

        throw new CannotCastValueToId($value);
    }

    public function unserialize(mixed $value, Type|null $type = null): mixed
    {
        // No need to implement this
        return $value;
    }
}
