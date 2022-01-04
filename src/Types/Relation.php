<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Relation extends BaseType
{
    public function __construct(
        public string $className
    )
    {
    }
}
