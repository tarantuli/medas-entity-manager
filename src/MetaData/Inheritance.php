<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

class Inheritance
{
    public bool $storeOriginalClass = false;
    public string $sharedParentClass;
    public string $originalClassStorageStrategy;

    public function __construct(
        public readonly string|null $parent,
    )
    {
    }
}
