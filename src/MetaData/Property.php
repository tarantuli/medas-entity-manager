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
        public bool                $isCreationTimestamp,
        public bool                $isModificationTimestamp,
        public bool                $isNullable,
        public bool                $isUnique,
        public array               $phpTypes,
        public \ReflectionProperty $reflection
    )
    {
    }

    public function __serialize(): array
    {
        return [
            $this->name,
            $this->type,
            $this->default,
            $this->isId,
            $this->isGeneratedValue,
            $this->isCreationTimestamp,
            $this->isModificationTimestamp,
            $this->isNullable,
            $this->isUnique,
            $this->phpTypes,
            $this->reflection->class,
            $this->reflection->name,
        ];
    }

    public function __unserialize(array $data): void
    {
        [
            $this->name,
            $this->type,
            $this->default,
            $this->isId,
            $this->isGeneratedValue,
            $this->isCreationTimestamp,
            $this->isModificationTimestamp,
            $this->isNullable,
            $this->isUnique,
            $this->phpTypes,
            $reflectionClass,
            $reflectionName,
        ] = $data;

        $this->reflection = new \ReflectionProperty($reflectionClass, $reflectionName);
    }

    public function allowsPhpType(string $type): bool
    {
        return ($type === 'null' && $this->isNullable) || in_array($type, $this->phpTypes);
    }
}
