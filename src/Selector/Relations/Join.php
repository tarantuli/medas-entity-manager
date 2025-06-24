<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Relations;

class Join implements Relation
{
    public static function c(
        string      $targetEntity,
        string|null $sourceProperty = null,
        string|null $targetProperty = null
    ): static
    {
        return new static($targetEntity, $sourceProperty, $targetProperty);
    }

    public function __construct(
        public string      $targetEntity,
        public string|null $sourceProperty = null,
        public string|null $targetProperty = null,
    )
    {
    }
}
