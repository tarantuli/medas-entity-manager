<?php

declare(strict_types=1);

namespace Medas\EntityManager\Filters;

/**
 * Matches when any of its sub-filters match - they are OR-ed together. Use it to
 * express an alternative inside an otherwise AND-ed condition set, e.g. "due, or
 * has no schedule yet":
 *
 *   new AnyOf(new LessThanOrEqual('scheduledAt', $now), new IsNull('scheduledAt'))
 */
class AnyOf implements Filter
{
    /** @var Filter[] */
    public array $filters;

    public function __construct(Filter ...$filters)
    {
        $this->filters = $filters;
    }
}
