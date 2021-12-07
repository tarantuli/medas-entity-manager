<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

class Table
{
    public function __construct(
        private Database $database,
        private string   $name,
    )
    {
    }
}
