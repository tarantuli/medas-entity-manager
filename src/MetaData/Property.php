<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

use Medas\EntityManager\Types\Type;

class Property
{
    public function __construct(
        public string              $name,
        public Type                $type,
        public bool                $hasDefault,
        public mixed               $default,
        public bool                $isId,
        public bool                $isGeneratedValue,
        public bool                $isCreationTimestamp,
        public bool                $isModificationTimestamp,
        public bool                $isNullable,
        public bool                $isUnique,
        public bool                $onDeleteCascade,
        public array               $phpTypes,
        public \ReflectionProperty $reflection
    )
    {
    }

    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'default' => $this->default,
            'isId' => $this->isId,
            'isGeneratedValue' => $this->isGeneratedValue,
            'isCreationTimestamp' => $this->isCreationTimestamp,
            'isModificationTimestamp' => $this->isModificationTimestamp,
            'isNullable' => $this->isNullable,
            'isUnique' => $this->isUnique,
            'onDeleteCascade' => $this->onDeleteCascade,
            'phpTypes' => $this->phpTypes,
            'reflectionClass' => $this->reflection->class,
            'reflectionName' => $this->reflection->name,
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
            $this->onDeleteCascade,
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
