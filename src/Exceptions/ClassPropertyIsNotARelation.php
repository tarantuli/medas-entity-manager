<?php

declare(strict_types=1);

namespace Medas\EntityManager\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\Core\Exceptions\Suggestions;

class ClassPropertyIsNotARelation extends BaseException implements Suggestions
{
    public function __construct(\ReflectionProperty $property)
    {
        parent::__construct($property->class, $property->name);
    }

    public function pattern(): string
    {
        return 'property %s::%s is not a manageable relation';
    }

    public function suggestions(): array
    {
        return [
            'if the relation is an entity, decorate it with Entity or implement HasId',
            'if the relation is an entity collection, decorate it with EntityCollection and implement ManagedCollection',
            '    extend LazyGenericCollection to implement ManagedCollection',
        ];
    }
}
