<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class OriginalClassNotFound extends BaseException
{
    public function __construct(
        string $entityClass,
        mixed  $id,
    )
    {
        parent::__construct($entityClass, $id);
    }

    public function pattern(): string
    {
        return 'Could not determine original class for %s with ID %s';
    }
}
