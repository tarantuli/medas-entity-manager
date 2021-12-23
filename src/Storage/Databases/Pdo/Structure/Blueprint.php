<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Structure;

use Medas\EntityManager\Storage\Databases\Pdo\Structure\Blueprint\Field;
use Medas\EntityManager\Storage\Databases\Pdo\Structure\Blueprint\Index;

class Blueprint
{
    public string $name;
    /** @var Field[] */
    public array $fields = [];
    /** @var Index[] */
    public array $indexes = [];

    public function addField(Field $field): void
    {
        $this->fields[$field->name] = $field;
    }

    public function addIndex(Index $index): void
    {
        $this->indexes[$index->name] = $index;
    }

    public function getField(string $name): Field
    {
        return $this->getFields([$name])[0];
    }

    public function getFields(array $names): array
    {
        return array_values(array_filter($this->fields, fn($field) => in_array($field->name, $names, true)));
    }
}
