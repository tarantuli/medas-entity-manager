<?php

declare(strict_types=1);

namespace Medas\EntityManager\Events;

/**
 * Raised once, after flush, for every entity hard-deleted in that flush.
 *
 * Soft-deletes do NOT arrive here - they are rewritten to updates by the
 * ChangeFinder and surface as EntityChanged with the soft-delete timestamp in
 * the diff.
 */
readonly class EntityDeleted
{
    public function __construct(
        public object $entity,
    )
    {
    }
}
