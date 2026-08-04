<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

class BackReference
{
    public function __construct(
        public string $name,
        public string $entity,
        public string $property,
    )
    {
    }
}
