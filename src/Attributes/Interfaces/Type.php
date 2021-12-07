<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes\Interfaces;

interface Type
{
    public function deserialize(mixed $value): mixed;
}
