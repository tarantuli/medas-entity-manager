<?php

declare(strict_types=1);

namespace Medas\EntityManager\Events;

/**
 * Raised once, after flush, for every entity newly inserted in that flush.
 *
 * Generic by design: listeners filter on $entity::class (or instanceof) for the
 * type they care about, rather than every creation needing its own event and an
 * explicit dispatch at each call site.
 */
readonly class EntityCreated
{
    public function __construct(
        public object $entity,
    )
    {
    }
}
