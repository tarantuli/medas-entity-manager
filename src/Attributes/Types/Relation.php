<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes\Types;

use Medas\EntityManager\Attributes\BaseType;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Relation extends BaseType
{
    public function __construct(
        public string $className
    )
    {
    }
}
