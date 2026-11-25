<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

// With a grouping a row stands for a group, not for one entity, and a count query returns
// a count per group, of which the Repository would take the first. Only a store-level
// query, which hands back the rows as they are, can select them.
class GroupingsThroughRepository extends BaseException
{
    public function __construct(public string $entity)
    {
        parent::__construct($entity);
    }

    public function pattern(): string
    {
        return 'a selector of %s has groupings, so its rows are groups rather than entities and its count'
            . ' is one per group: select them with a store-level query (DefinitionQueryBuilder in pdo-storage)';
    }
}
