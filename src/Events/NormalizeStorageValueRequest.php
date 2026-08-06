<?php

declare(strict_types=1);

namespace Medas\EntityManager\Events;

/**
 * Dispatched by the Hydrator's ValueCaster when a value read from storage needs
 * to be normalized to its backend-agnostic form (an array) before being cast to
 * a #[DataHolder] value object.
 *
 * The backend that produced the value listens and converts it - e.g.
 * pdo-storage decodes its JSON varchar - so entity-manager never learns how any
 * backend represents the value. This is the read-side counterpart to the write
 * side's SerializeValueRequest, dispatched from the type authority rather than
 * the storage layer, because only the type authority knows a value needs
 * normalizing at all.
 *
 * normalizedValue defaults to the raw value, so if nothing handles the request
 * (a backend that already returns a native array, or a test without pdo) the
 * value passes through unchanged.
 */
class NormalizeStorageValueRequest
{
    public mixed $normalizedValue;

    public function __construct(
        public readonly mixed $value,
    )
    {
        $this->normalizedValue = $value;
    }
}
