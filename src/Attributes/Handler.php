<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class Handler
{
    public function __construct(
        public string|null $className = null,
    )
    {
    }
}
