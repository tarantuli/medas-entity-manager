<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConfigOptions;

use Medas\ServiceManager\AsSingleton;
use Medas\ServiceManager\ConfigOptions\{ConfigGroup, ConfigOption};

class EntityDirectories implements ConfigOption
{
    use AsSingleton;

    public function group(): ConfigGroup
    {
        return EntityManagerGroup::instance();
    }

    public function name(): string
    {
        return 'entity-directories';
    }

    public function description(): string
    {
        return 'The directories where entity files reside.';
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
