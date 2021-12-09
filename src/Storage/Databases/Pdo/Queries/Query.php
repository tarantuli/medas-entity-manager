<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

use Medas\EntityManager\Storage\Databases\Pdo\Database;

class Query
{
    public Database $database;
    public \Closure|null $onComplete = null;

    public function __construct(
        public string $query,
        public array  $arguments
    )
    {
    }

    public function onComplete(\Closure|null $onComplete): self
    {
        $this->onComplete = $onComplete;

        return $this;
    }

    public function database(Database $database): self
    {
        $this->database = $database;

        return $this;
    }
}
