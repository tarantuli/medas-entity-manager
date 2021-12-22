<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Structure;

class Blueprint
{
    public string $name;
    /** @var Blueprint\Field[] */
    public array $fields = [];
    public string $engine;
    public string $collation;
    /** @var Blueprint\Index[] */
    public array $indexes = [];

    public function addField(Blueprint\Field $field): void
    {
        $this->fields[$field->name] = $field;
    }

    public function addIndex(Blueprint\Index $index): void
    {
        $this->indexes[$index->name] = $index;
    }

    public function getFields(array $names): array
    {
        return array_values(array_filter($this->fields, fn($field) => in_array($field->name, $names, true)));
    }
}
