<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConfigOptions;

use Medas\ServiceManager\AsSingleton;
use Medas\ServiceManager\ConfigOptions\ConfigGroup;

class EntityManagerGroup implements ConfigGroup
{
    use AsSingleton;

    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'entity-manager';
    }
}
