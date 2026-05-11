<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

use Medas\EntityManager\Interfaces\OwnershipFilter;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AddOwnershipFilter
{
    /** @var class-string<OwnershipFilter>[] */
    private array $classNames;

    public function __construct(...$classNames)
    {
        $this->classNames = $classNames;
    }
}
