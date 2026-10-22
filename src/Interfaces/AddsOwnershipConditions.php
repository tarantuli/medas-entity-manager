<?php

declare(strict_types=1);

namespace Medas\EntityManager\Interfaces;

use Medas\EntityManager\Selector\Conditions\Condition;

/**
 * An ownership filter that also restricts the query with selector conditions, for a
 * restriction the REST filter syntax cannot write: one that reaches the owner along
 * a back-reference, say (see WhereReferencedBy). Its conditions apply wherever its
 * filters do, so paging and the total count only see rows the caller may read.
 */
interface AddsOwnershipConditions extends OwnershipFilter
{
    /** @return Condition[] */
    public function addConditions(): array;
}
