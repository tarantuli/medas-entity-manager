<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

use Medas\EntityManager\Storage\Interfaces\StoreRecord;

class Record implements StoreRecord
{
    public function __construct(private array $data)
    {
    }

    public function get(string $name)
    {
        return $this->data[$name];
    }

    public function data(): array
    {
        return $this->data;
    }
}
