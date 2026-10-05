<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

/**
 * Marks the identity property of an entity.
 *
 * The id is part of every entity's read/write contract without a further
 * attribute: it is always readable, and writable on creation only. A client may
 * therefore supply the id of the entity it creates - which is what lets it address
 * that entity straight away, before the response arrives - and can never change
 * the id of an existing one. When no id is supplied, one is generated on persist.
 *
 * This contract is applied by the metadata compiler, deliberately not by
 * extending IsReadable: an attribute class has one parent, so inheritance could
 * express "readable" but not "writable on creation" as well.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Id
{
}
