<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ReferencedByPropertyMustBeReferenceCollection extends BaseException
{
    public function __construct(\ReflectionProperty $property, string $declaredType)
    {
        parent::__construct($property->class, $property->name, $declaredType);
    }

    public function pattern(): string
    {
        return 'property %s:%s is tagged #[ReferencedBy] and must be typed as a ReferenceCollection, but is declared as %s';
    }
}
