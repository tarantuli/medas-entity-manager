<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Selectors;

use Medas\Core\Interfaces\NotCacheable;
use Medas\EntityManager\Selector\{Definition, Selector, Slice};

/**
 * Selects only the first record that another selector selects, so a storage can stop at one row instead of returning
 * every match.
 *
 * Not cacheable: a storage caches a compiled selector by its class, and every FirstOf shares one class whatever it
 * wraps.
 */
readonly class FirstOf implements Selector, NotCacheable
{
    public function __construct(
        private Selector $selector,
    )
    {
    }

    public function entity(): string
    {
        return $this->selector->entity();
    }

    public function definition(): Definition
    {
        // Cloned, as a selector may hand out the same Definition instance every time
        $definition = clone $this->selector->definition();
        $slice = $definition->slice;

        $definition->slice = $slice === null
            ? Slice::c(1)
            : Slice::c(min($slice->count, 1), $slice->from);

        return $definition;
    }
}
