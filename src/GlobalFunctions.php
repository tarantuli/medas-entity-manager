<?php

declare(strict_types=1);

// This file should be in the global namespace

use Medas\Core\GlobalRepository;
use Medas\EntityManager\EntityManager;

function em(): EntityManager
{
    return GlobalRepository::serviceManager()->resolve(EntityManager::class);
}
