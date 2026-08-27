<?php

declare(strict_types=1);

namespace Medas\EntityManager\Events;

use Medas\EntityManager\Snapshots\PropertyChange;

/**
 * Raised once, after flush, for every entity whose properties changed in that
 * flush. $changes is the per-property diff (keyed by property name), so a
 * listener can filter both on $entity::class and on which fields moved -
 * e.g., isset($changes['endDate']).
 *
 * Note: a soft-delete surfaces here (not as EntityDeleted), as a change to the
 * soft-delete timestamp - the ChangeFinder rewrites soft-deletes to updates.
 *
 * @property array<string, PropertyChange> $changes
 */
readonly class EntityChanged
{
    /** @param array<string, PropertyChange> $changes */
    public function __construct(
        public object $entity,
        public array  $changes,
    )
    {
    }
}
