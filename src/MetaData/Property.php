<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

use Medas\Core\Interfaces\Type;
use Medas\EntityManager\Attributes\Relations\Action;

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
        public Action              $onDelete,
        public Action              $onUpdate,
        public array               $phpTypes,
        public \ReflectionProperty $reflection,
        public string|null         $handler,

        /** @var object[] */
        public array               $attributes = [],
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
            'onDelete' => $this->onDelete,
            'onUpdate' => $this->onUpdate,
            'phpTypes' => $this->phpTypes,
            'reflectionClass' => $this->reflection->class,
            'reflectionName' => $this->reflection->name,
            'handler' => $this->handler,
            'attributes' => $this->attributes,
        ];
    }

    public function __unserialize(array $data): void
    {
        $values = array_values($data);

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
            $this->onDelete,
            $this->onUpdate,
            $this->phpTypes,
            $reflectionClass,
            $reflectionName,
            $this->handler,
        ] = $values;

        // Tolerate metadata cached before this field existed.
        $this->attributes = $values[17] ?? [];
        $this->reflection = new \ReflectionProperty($reflectionClass, $reflectionName);
    }

    // The first attribute on this property that is an instance of $class, or null.
    // Reads from the additional-attributes bag - owned attributes have typed fields.
    public function attribute(string $class): object|null
    {
        return array_find($this->attributes ?? [], fn($attribute) => $attribute instanceof $class);
    }

    public function allowsPhpType(string $type): bool
    {
        return ($type === 'null' && $this->isNullable)
            || in_array($type, $this->phpTypes)
            || in_array('mixed', $this->phpTypes);
    }
}
