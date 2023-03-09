<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Interfaces\{Guid, HasId, StringSerializer, Type};

#[Service]
class IdSerializer implements StringSerializer
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

    public function unserialize(Type $type, mixed $value): mixed
    {
        // No need to implement this
        return $value;
    }
}
