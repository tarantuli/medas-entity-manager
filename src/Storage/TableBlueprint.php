<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage;

use Medas\EntityManager\Attributes\Interfaces\Type;

class TableBlueprint implements \Medas\ServiceManager\Interfaces\Storage\TableBlueprint
{
    /** @var Type[] $fields */
    private array $fields = [];

    public function __construct(private string $name)
    {
    }

    public function addField(string $name, Type $type): self
    {
        $this->fields[$name] = $type;
        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function fields(): array
    {
        return $this->fields;
    }
}
