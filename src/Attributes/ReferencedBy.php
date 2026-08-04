<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ReferencedBy
{
    public function __construct(
        public string      $entity,
        public string|null $property = null,
    )
    {
    }
}
