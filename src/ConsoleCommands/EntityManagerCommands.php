<?php

declare(strict_types=1);

namespace Medas\EntityManager\ConsoleCommands;

use Medas\Console\Commands\{BaseConsoleCommandGroup, ConsoleCommandGroup};
use Medas\ServiceManager\Service;

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
