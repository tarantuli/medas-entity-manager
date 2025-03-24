<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes\Entity;

#[\Attribute(\Attribute::TARGET_CLASS)]
class StorageConfigOption
{
    public function __construct(
        public string $className,
    )
    {
    }
}
