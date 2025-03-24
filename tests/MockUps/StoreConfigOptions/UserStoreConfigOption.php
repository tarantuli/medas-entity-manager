<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps\StoreConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class UserStoreConfigOption implements ConfigOption
{
    public function __construct(
        private StoresConfigGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'users';
    }

    public function description(): string
    {
        return 'The name of the store for User entities';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): string
    {
        return 'atypical-user-store';
    }
}
