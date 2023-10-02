<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption, Interfaces\Validator};

#[Service]
readonly class EntityDirectories implements ConfigOption, Validator
{
    public function __construct(
        private EntityManagerGroup $group,
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

    public function isValid(mixed $value): bool
    {
        return is_array($value);
    }
}
