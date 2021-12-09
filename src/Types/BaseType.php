<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

use Medas\EntityManager\Attributes\Interfaces\Type;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
abstract class BaseType implements Type
{
    public function deserialize(mixed $value): mixed
    {
        return $value;
    }

    public function serialize(mixed $value): mixed
    {
        return $value;
    }
}
