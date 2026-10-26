<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes\Changes;

/**
 * Marks a relation to the entity that owns this one: every change logged for this entity
 * is also listed in the owner's history. Followed transitively, so a price tier marked
 * towards its sale price, which is marked towards its product, shows up in both.
 *
 * Put it on the relation property of the owned entity (the side holding the reference),
 * not on the owner's back-reference collection.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class LogChangesTo
{
}
