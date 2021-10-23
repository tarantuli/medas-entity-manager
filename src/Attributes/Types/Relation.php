<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes\Types;

use Medas\EntityManager\Attributes\Stored;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Relation extends Stored
{
    public function __construct(
        public string $className
    )
    {
    }
}
