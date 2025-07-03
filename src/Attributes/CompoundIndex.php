<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class CompoundIndex
{
    /** @var string[] */
    public array $properties;

    public function __construct(string ...$properties)
    {
        $this->properties = $properties;
    }
}
