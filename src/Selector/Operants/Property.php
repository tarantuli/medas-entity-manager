<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Operants;

class Property implements Operant
{
    public static function c(string $name, string|null $entity = null): static
    {
        return new static($name, $entity);
    }

    public function __construct(
        public string      $name,
        public string|null $entity = null,
    )
    {
    }
}
