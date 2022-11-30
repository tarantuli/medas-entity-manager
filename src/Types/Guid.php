<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Guid extends Binary
{
    public function __construct()
    {
        parent::__construct(16, 16);
    }
}
