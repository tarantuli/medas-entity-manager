<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

class Query
{
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
}
