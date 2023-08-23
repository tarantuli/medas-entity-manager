<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConfigOptions;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\{ConfigGroup, ConfigOption};

#[Service]
readonly class GeneratorRootNamespace implements ConfigOption
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
        return 'generator-root-namespace';
    }

    public function description(): string
    {
        return 'The root namespace to use when the entity name starts with a dot (eg. ./Projects/Project)';
    }

    public function hasDefault(): bool
    {
        return false;
    }

    public function default(): null
    {
        return null;
    }
}
