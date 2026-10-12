<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\{
    Attributes\Service,
    Interfaces\HasId,
    Interfaces\StringSerializer,
    Interfaces\Type,
    Interfaces\Uuid
};
use Medas\EntityManager\Exceptions\CannotCastValueToId;

#[Service]
readonly class IdSerializer implements StringSerializer
{
    private const string UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public function serialize(mixed $value): string
    {
        if ($value instanceof HasId) {
            $value = $value->id();
        }

        if ($value instanceof Uuid) {
            $value = $value->toBytes();
        }

        // A Uuid's text form, as an id from a URL or request body arrives, keys the same entity as the Uuid itself.
        // Without this, get() with the text and get() with the Uuid returned two instances of the same row, which
        // breaks identity checks and lets one instance's changes overwrite the other's.
        if (is_string($value) && preg_match(self::UUID_PATTERN, $value)) {
            $value = hex2bin(str_replace('-', '', $value));
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
