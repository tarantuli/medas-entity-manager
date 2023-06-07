<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
class EntityDirectories implements ConfigOption
{
    public function __construct(
        private readonly EntityManagerGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'entity-directories';
    }

    public function description(): string
    {
        return 'The directories where entity files reside';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): array
    {
        return ['src'];
    }
}
