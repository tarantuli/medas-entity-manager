<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

use Medas\EntityManager\Types\Type;

class Property
{
    public function __construct(
        public string              $name,
        public Type                $type,
        public mixed               $default,
        public bool                $isId,
        public bool                $isGeneratedValue,
        public bool                $isNullable,
        public bool                $isUnique,
        public array               $phpTypes,
        public \ReflectionProperty $reflection
    )
    {
    }

    public function allowsPhpType(string $type): bool
    {
        return ($type === 'null' && $this->isNullable) || in_array($type, $this->phpTypes);
    }
}
