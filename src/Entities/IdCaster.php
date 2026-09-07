<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\{
    Attributes\Service,
    Exceptions\UuidProviderIsNotAvailable,
    Interfaces\Uuid,
    Interfaces\UuidProvider
};
use Medas\EntityManager\{
    Exceptions\CannotCastValueToId,
    Exceptions\EntityHasNoIdProperty,
    MetaDataManager
};

// Casts a scalar id - as request input and config deliver it, a string or int -
// to the concrete type the target entity is keyed by. Most entities key on a
// Uuid, but not all: a string key (a Locale's 'nl_NL', an email template's
// constant) or an int key is equally valid. The id property's own declared type
// decides the conversion, so no caller has to assume Uuid.
#[Service]
readonly class IdCaster
{
    public function __construct(
        private MetaDataManager   $metaDataManager,
        private UuidProvider|null $uuidProvider,
    )
    {
    }

    public function cast(string $entityClass, mixed $value): mixed
    {
        // Only a scalar stands in for an id; an already-hydrated id (or null)
        // passes through untouched.
        if (!is_scalar($value)) {
            return $value;
        }

        $idProperty
            = $this->metaDataManager->get($entityClass)->idProperty ?? throw new EntityHasNoIdProperty($entityClass);

        if (in_array(Uuid::class, $idProperty->phpTypes, true)) {
            if ($this->uuidProvider === null) {
                throw new UuidProviderIsNotAvailable();
            }

            return $this->uuidProvider->fromString((string) $value);
        }

        if (in_array('string', $idProperty->phpTypes, true)) {
            return (string) $value;
        }

        if (in_array('int', $idProperty->phpTypes, true)) {
            return (int) $value;
        }

        throw new CannotCastValueToId($value);
    }
}
