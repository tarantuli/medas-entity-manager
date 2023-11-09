<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\ValueFetchers;

class FetchResult
{
    public function __construct(
        public bool  $foundValue,
        public mixed $value = null,
    )
    {
    }
}
