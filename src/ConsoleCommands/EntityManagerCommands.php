<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConsoleCommands;

use Medas\Console\Commands\BaseConsoleCommandGroup;
use Medas\Console\Commands\ConsoleCommandGroup;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityManagerCommands extends BaseConsoleCommandGroup
{
    public function parent(): ConsoleCommandGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'entity-manager';
    }
}
