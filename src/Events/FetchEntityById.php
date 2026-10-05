<?php

declare(strict_types=1);

namespace Medas\EntityManager\Events;

/**
 * Asks for the entity with the given id, leaving $entity null when no such row
 * exists - Repository::fetchById() as an event.
 *
 * For callers that cannot depend on the Repository without closing a dependency
 * circle: a serializer is itself a dependency of the storage layer the Repository
 * is built on. Unlike FindEntity, which is answered by EntityManager::get() and so
 * always yields an entity, this one checks.
 */
class FetchEntityById
{
    public object|null $entity = null;

    public function __construct(
        public readonly string $type,
        public readonly mixed  $id,
    )
    {
    }
}
