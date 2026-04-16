<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};
use Medas\EntityManager\Attributes\Relations\Action;

#[Service]
readonly class DefaultOnDeleteAction implements ConfigOption
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
        return 'default-on-delete-action';
    }

    public function description(): string
    {
        return 'The default action to take when a referenced entity is deleted';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): Action
    {
        return Action::Restrict;
    }
}
