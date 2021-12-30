<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Structure\Blueprint;

class Field
{
    public function __construct(
        public string $name,
        public string $definition,
    )
    {
    }
}
