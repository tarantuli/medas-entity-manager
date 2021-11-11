<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

class Query
{
    public function __construct(private string $query, private array $arguments = [])
    {
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }
}
