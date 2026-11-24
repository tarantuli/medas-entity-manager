<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\ValueFetchers;

class FetchResult
{
    public function __construct(
        public bool  $foundValue,
        public mixed $value = null,

        // For a fetch of records: per record, by the same index, the derived values its
        // query selected, by name
        public array $derivedValues = [],
    )
    {
    }
}
