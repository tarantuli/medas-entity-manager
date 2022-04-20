<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Selectors;

use Medas\EntityManager\Selector\Definition;
use Medas\EntityManager\Selector\Selector;
use Medas\ServiceManager\Interfaces\NotCacheable;

class AllEntities implements Selector, NotCacheable
{
    public function __construct(
        private readonly string $entity
    )
    {
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function get(): Definition
    {
        return new Definition($this->entity);
    }
}
