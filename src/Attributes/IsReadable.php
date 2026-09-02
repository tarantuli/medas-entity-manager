<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

/**
 * Opt-in marker for a property or method that belongs to an entity's readable
 * representation, so a metadata-driven serializer emits it - and anything not
 * marked stays hidden.
 *
 * On a property, the value is emitted directly. On a method, the method is
 * called with no arguments and its return value is emitted as a computed field:
 * the home for derived values (a flattened relation, a formatted label).
 *
 * The output field takes the property or method name, unless $name overrides it
 * - renaming a property, or naming a computed method's field.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class IsReadable
{
    public function __construct(
        public string|null $name = null,
    )
    {
    }
}
