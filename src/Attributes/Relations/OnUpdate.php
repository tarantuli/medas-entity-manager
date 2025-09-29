<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes\Relations;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class OnUpdate
{
    public function __construct(
        public Action $action,
    )
    {
    }
}
