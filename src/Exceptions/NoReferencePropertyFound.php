<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class NoReferencePropertyFound extends BaseException
{
    public function __construct(
        string $sourceProperty,
        string $targetEntity,
        string $sourceEntity,
    )
    {
        parent::__construct($sourceProperty, $targetEntity, $sourceEntity);
    }

    public function pattern(): string
    {
        return 'Property %s should be referenced by %s but no properties refer to %s';
    }
}
