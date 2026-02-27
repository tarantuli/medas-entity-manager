<?php

declare(strict_types=1);

namespace Medas\EntityManager\Events;

class FindEntity
{
    public object|null $entity = null;

    public function __construct(
        public readonly string $type,
        public readonly mixed  $id,
    )
    {
    }
}
