<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Selectors;

use Medas\EntityManager\Selector\{Conditions\WhereIs, Definition, Operants\Property, Operants\Value, Selector};
use Medas\ServiceManager\Cache\Interfaces\NotCacheable;

class WithValues implements Selector, NotCacheable
{
    public function __construct(
        private readonly string $entity,
        private readonly array  $values,
    )
    {
    }

    public function definition(): Definition
    {
        $definition = new Definition($this->entity);

        foreach ($this->values as $property => $value) {
            $definition->add(new WhereIs(new Property($property), new Value($value)));
        }

        return $definition;
    }
}
