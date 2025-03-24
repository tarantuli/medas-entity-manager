<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup};

#[Service]
readonly class StoreNamesGroup implements ConfigGroup
{
    public function __construct(
        private EntityManagerGroup $group,
    )
    {
    }

    public function parent(): ConfigGroup|null
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'store-names';
    }
}
