<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class EntityCollection
{
    public function __construct(
        public string $contentType,
    )
    {
    }
}
