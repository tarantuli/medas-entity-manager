<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class MultipleReferencePropertiesFound extends BaseException
{
    public function __construct(
        string $sourceProperty,
        string $targetEntity,
        string $sourceEntity,
        array  $foundProperties,
    )
    {
        parent::__construct(
            $sourceProperty,
            $targetEntity,
            $sourceEntity,
            implode(', ', $foundProperties)
        );
    }

    public function pattern(): string
    {
        return 'Property %s should be referenced by %s but multiple properties in %s refer to %s: %s';
    }
}
