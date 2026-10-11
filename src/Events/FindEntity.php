<?php

declare(strict_types=1);

namespace Medas\EntityManager\Events;

/**
 * Asks for the entity with the given id, for a relation. Answered by @class(Medas\EntityManager\EntityManager).
 *
 * With ```$lazy``` (the default), an entity that isn't loaded yet is returned as a lazy ghost with only its id set,
 * which is loaded on first access to any other property. So relations that are never followed cost no query.
 */
class FindEntity
{
    public object|null $entity = null;

    public function __construct(
        public readonly string $type,
        public readonly mixed  $id,
        public readonly bool   $lazy = true,
    )
    {
    }
}
