<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Selectors;

use Medas\Core\Interfaces\NotCacheable;
use Medas\EntityManager\Selector\{Definition, Selector};

readonly class AllEntities implements Selector, NotCacheable
{
    public function __construct(
        private string $entity
    )
    {
    }

    public function definition(): Definition
    {
        return new Definition($this->entity);
    }
}
