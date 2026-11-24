<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Relations;

/**
 * Keeps the rows that have no related row, with the joined entity's properties null,
 * where a plain Join (an inner join) leaves them out.
 */
class LeftJoin extends Join
{
}
