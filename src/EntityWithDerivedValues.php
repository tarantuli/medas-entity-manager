<?php

declare(strict_types=1);

namespace Medas\EntityManager;

/**
 * A row of Repository::fetchWithDerivedValues(): the entity, and the derived values
 * its query selected for it, by name.
 */
readonly class EntityWithDerivedValues
{
    /** @param array<string, mixed> $values */
    public function __construct(
        public object $entity,
        public array  $values,
    )
    {
    }
}
