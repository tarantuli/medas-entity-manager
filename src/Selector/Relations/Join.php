<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Relations;

class Join implements Relation
{
    public function __construct(
        public string      $targetEntity,
        public string|null $sourceProperty = null,
        public string|null $targetProperty = null,
    )
    {
    }
}
