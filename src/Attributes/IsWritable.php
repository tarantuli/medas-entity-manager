<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

/**
 * Opt-in marker for a property or method that may be written from an incoming
 * payload. A property with no IsWritable is never settable from a request,
 * whatever the payload carries - the fail-closed default that keeps fields like
 * a resolved price out of the client's reach.
 *
 * $onCreate and $onUpdate choose which operations accept the field; both default
 * to true (fully writable). Set onUpdate: false for a set-once value - assigned
 * at creation, immutable afterwards - such as a foreign key like a
 * subscription's order or its tenant.
 *
 * The incoming field takes the property or method name, unless $name overrides
 * it (e.g., a setPassword() method exposed as the field 'password').
 *
 * $setter routes the value through a method rather than a direct assignment: on
 * a property, when given, the value is passed to that named method instead of
 * being assigned to the property (null assigns directly); a method target is
 * itself the setter and is called with the value.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class IsWritable
{
    public function __construct(
        public bool        $onCreate = true,
        public bool        $onUpdate = true,
        public string|null $name = null,
        public string|null $setter = null,
    )
    {
    }
}
