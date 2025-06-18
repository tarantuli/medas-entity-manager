<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

use Medas\Core\Interfaces\Type;

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
        public bool                $isIndex,
        public bool                $onDeleteCascade,
        public array               $phpTypes,
        public \ReflectionProperty $reflection,
        public string|null         $handler,
    )
    {
    }

    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'hasDefault' => $this->hasDefault,
            'default' => $this->default,
            'isId' => $this->isId,
            'isGeneratedValue' => $this->isGeneratedValue,
            'isCreationTimestamp' => $this->isCreationTimestamp,
            'isModificationTimestamp' => $this->isModificationTimestamp,
            'isNullable' => $this->isNullable,
            'isUnique' => $this->isUnique,
            'isIndex' => $this->isIndex,
            'onDeleteCascade' => $this->onDeleteCascade,
            'phpTypes' => $this->phpTypes,
            'reflectionClass' => $this->reflection->class,
            'reflectionName' => $this->reflection->name,
            'handler' => $this->handler,
        ];
    }

    public function __unserialize(array $data): void
    {
        [
            $this->name,
            $this->type,
            $this->hasDefault,
            $this->default,
            $this->isId,
            $this->isGeneratedValue,
            $this->isCreationTimestamp,
            $this->isModificationTimestamp,
            $this->isNullable,
            $this->isUnique,
            $this->isIndex,
            $this->onDeleteCascade,
            $this->phpTypes,
            $reflectionClass,
            $reflectionName,
            $this->handler,
        ] = array_values($data);

        $this->reflection = new \ReflectionProperty($reflectionClass, $reflectionName);
    }

    public function allowsPhpType(string $type): bool
    {
        return ($type === 'null' && $this->isNullable)
            || in_array($type, $this->phpTypes)
            || in_array('mixed', $this->phpTypes);
    }
}
