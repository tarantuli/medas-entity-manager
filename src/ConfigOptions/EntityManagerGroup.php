<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConfigOptions;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\ConfigGroup;

#[Service]
readonly class EntityManagerGroup implements ConfigGroup
{
    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'entity-manager';
    }
}
