<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

interface Type
{
    public function serialize(mixed $value): mixed;

    public function deserialize(mixed $value): mixed;
}
