<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

/**
 * Marks an entity whose rows are written once and never changed afterwards, such as log entries.
 *
 * Once such an entity has been inserted, or when it is fetched with get(), the entity manager stops
 * tracking its changes: it stays in the identity map, but flush() no longer diffs it. Anything
 * changed on it after that is therefore not saved. Deleting it still works.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AppendOnly
{
}
