<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

use Medas\EntityManager\Interfaces\OwnershipFilter;

#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class AddOwnershipFilter
{
    /** @var class-string<OwnershipFilter>[] */
    public array $filters;

    public function __construct(...$filters)
    {
        $this->filters = $filters;
    }
}
