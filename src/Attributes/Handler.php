<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Handler
{
    public function __construct(
        public readonly string|null $className = null,
    )
    {
    }
}
