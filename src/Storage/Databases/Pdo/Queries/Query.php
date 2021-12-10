<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

use Medas\EntityManager\Storage\BaseAction;
use Medas\EntityManager\Storage\Interfaces\Storage;

class Query extends BaseAction
{
    public function __construct(
        public string $query,
        public array  $arguments,
        Storage       $storage
    )
    {
        $this->storage = $storage;
    }

    public function execute(): void
    {
        $this->storage->execute($this);
    }
}
