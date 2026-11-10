<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\EntityManager\Snapshots\Changes;

class FlushDoesNotSettle extends BaseException
{
    public function __construct(int $rounds, Changes $changes)
    {
        $classes = array_map(
            fn(object $entity) => $entity::class,
            [
                ...$changes->createdEntities(),
                ...$changes->updatedEntities(),
                ...$changes->deletedEntities()
            ]
        );

        parent::__construct($rounds, implode(', ', array_unique($classes)));
    }

    public function pattern(): string
    {
        return 'flush did not settle after %s rounds: after-flush handlers keep changing entities, last of all %s';
    }
}
