<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

// Output values replace the entity's columns: the Repository would cache such a row as
// the entity's record, and hydrating it fails or finds no id. Only a store-level query,
// which hands back the rows as they are, can select them.
class OutputValuesThroughRepository extends BaseException
{
    public function __construct(public string $entity)
    {
        parent::__construct($entity);
    }

    public function pattern(): string
    {
        return 'a selector of %s has output values, which replace the entity\'s columns, so the Repository'
            . ' cannot turn its rows into entities: select them with a store-level query'
            . ' (DefinitionQueryBuilder in pdo-storage)';
    }
}
