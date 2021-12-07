<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

class Query
{
    public function __construct(
        public string $query,
        public array  $arguments
    )
    {
    }
}
