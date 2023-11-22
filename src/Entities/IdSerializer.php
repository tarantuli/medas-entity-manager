<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\{
    Attributes\Service,
    Interfaces\Guid,
    Interfaces\HasId,
    Interfaces\StringSerializer,
    Interfaces\Type
};

#[Service]
readonly class IdSerializer implements StringSerializer
{
    public function serialize(mixed $value): string
    {
        if ($value instanceof HasId) {
            $value = $value->id();
        }

        if ($value instanceof Guid) {
            $value = $value->toBytes();
        }

        return (string) $value;
    }

    public function unserialize(mixed $value, Type $type = null): mixed
    {
        // No need to implement this
        return $value;
    }
}
