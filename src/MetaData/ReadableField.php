<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

/**
 * A resolved entry in an entity's readable contract: one field a serializer
 * emits. Reflected from an #[IsReadable] on a property or method and cached with
 * the rest of the metadata, so it carries names and flags only - no reflection
 * object - and serializes with the MetaData.
 */
class ReadableField
{
    public function __construct(
        // The property or method on the entity that supplies the value.
        public string $source,

        // Whether $source is a method to call (computed field) rather than a
        // property to read.
        public bool   $isMethod,

        // The field name in the output.
        public string $name,
    )
    {
    }
}
