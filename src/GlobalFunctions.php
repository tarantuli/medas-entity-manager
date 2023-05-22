<?php

declare(strict_types=1);

// This file should be in the global namespace

use Medas\EntityManager\EntityManager;

function em(): EntityManager
{
    return medas()->serviceManager()->resolve(EntityManager::class);
}
