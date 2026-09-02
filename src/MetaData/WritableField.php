<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

/**
 * A resolved entry in an entity's writable contract: one field a denormalizer
 * may accept from a payload. Reflected from an #[IsWritable] on a property or
 * method and cached with the rest of the metadata.
 *
 * How the value is applied:
 *  - method target ($isMethod true): call $source($value);
 *  - property target with a $setter: call $setter($value);
 *  - property target without a $setter: assign the property directly.
 */
class WritableField
{
    public function __construct(
        // The property or method on the entity the value targets.
        public string      $source,

        // Whether $source is a method to call with the value.
        public bool        $isMethod,

        // The field name expected in the payload.
        public string      $name,

        // Property targets only: a setter method to route the value through,
        // or null to assign the property directly.
        public string|null $setter,
        public bool        $onCreate,
        public bool        $onUpdate,
    )
    {
    }
}
