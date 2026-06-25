<?php

declare(strict_types=1);

namespace Medas\EntityManager\Filters;

class LessThanOrEqual implements Filter
{
    public function __construct(
        public string $field,
        public mixed  $value,
    )
    {
    }
}
