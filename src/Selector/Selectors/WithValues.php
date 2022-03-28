<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Selectors;

use Medas\EntityManager\Selector\Conditions\WhereIs;
use Medas\EntityManager\Selector\Definition;
use Medas\EntityManager\Selector\Operants\Property;
use Medas\EntityManager\Selector\Operants\Value;
use Medas\EntityManager\Selector\Selector;

class WithValues implements Selector
{
    public function __construct(
        private string $entity,
        private array  $values,
    )
    {
    }

    public function get(): Definition
    {
        $definition = new Definition($this->entity);

        foreach ($this->values as $property => $value) {
            $definition->add(new WhereIs(new Property($property), new Value($value)));
        }

        return $definition;
    }
}
