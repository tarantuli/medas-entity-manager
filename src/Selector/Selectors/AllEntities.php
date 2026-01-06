<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Selectors;

use Medas\Core\Interfaces\NotCacheable;
use Medas\EntityManager\Selector\{Definition, Selector};

readonly class AllEntities implements Selector, NotCacheable
{
    private Definition $definition;

    public function __construct(
        private string $entity,
    )
    {
        $this->definition = new Definition($this->entity);
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function definition(): Definition
    {
        return $this->definition;
    }
}
