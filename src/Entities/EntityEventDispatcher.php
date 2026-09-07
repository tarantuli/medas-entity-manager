<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{
    Events\EntityChanged,
    Events\EntityCreated,
    Events\EntityDeleted,
    Snapshots\Changes
};

/**
 * Turns the categorised persistence changes of a flush into generic domain
 * events - EntityCreated / EntityChanged / EntityDeleted - one per affected
 * entity. Runs as an AfterFlushHandler, so it fires post-commit and covers every
 * write path (REST, console, cron, seed) without a dispatch at each call site.
 *
 * Returns false: it only emits events, it does not itself dirty the unit of
 * work. If a listener persists in response, that entity is picked up by the next
 * flush (its own, or the request's closing flush) and dispatched in turn.
 */
#[Service]
readonly class EntityEventDispatcher implements AfterFlushHandler
{
    public function handle(Changes $changes): bool
    {
        foreach ($changes->createdEntities() as $entity) {
            dispatch(new EntityCreated($entity));
        }

        foreach ($changes->updatedEntities() as $entity) {
            dispatch(new EntityChanged($entity, $changes->entityChanges($entity)));
        }

        foreach ($changes->deletedEntities() as $entity) {
            dispatch(new EntityDeleted($entity));
        }

        return false;
    }
}
