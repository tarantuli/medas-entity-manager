<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};
use Medas\EntityManager\Attributes\Relations\Action;

#[Service]
readonly class DefaultOnUpdateAction implements ConfigOption
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
        return 'default-on-update-action';
    }

    public function description(): string
    {
        return 'The default action to take when the ID of a referenced entity is updated';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): Action
    {
        return Action::Cascade;
    }
}
