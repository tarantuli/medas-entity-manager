<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Entity
{
    public function __construct(
        public ?string $db = null
    )
    {
    }
}
