<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConfigOptions;

use Medas\Core\AsSingleton;
use Medas\Core\Interfaces\{ConfigGroup, ConfigOption};

class GeneratorRootNamespace implements ConfigOption
{
    use AsSingleton;

    public function group(): ConfigGroup
    {
        return EntityManagerGroup::instance();
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

    public function default(): mixed
    {
        return null;
    }
}
