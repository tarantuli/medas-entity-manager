<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Structure\TypeHandlers;

use Medas\EntityManager\MetaData\Property;

interface TypeHandler
{
    public function getFieldType(Property $property): string;
}
