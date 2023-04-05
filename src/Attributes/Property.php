<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Property
{
    public function __construct(
        public readonly string|null $handler = null,
    )
    {
    }
}
