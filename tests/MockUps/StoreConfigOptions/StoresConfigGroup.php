<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\StoreConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup};

#[Service]
readonly class StoresConfigGroup implements ConfigGroup
{
    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'stores';
    }
}
