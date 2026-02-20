<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ReferenceFetchFailed extends BaseException
{
    public function __construct(
        string $entityClass,
        string $propertyName,
        string $referenceEntity,
    )
    {
        parent::__construct($entityClass, $propertyName, $referenceEntity);
    }

    public function pattern(): string
    {
        return 'Failed to fetch references for %s::%s (referencing %s)';
    }
}
