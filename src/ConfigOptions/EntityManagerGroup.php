<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConfigOptions;

use Medas\Core\Interfaces\ConfigGroup;
use Medas\ServiceManager\AsSingleton;

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
