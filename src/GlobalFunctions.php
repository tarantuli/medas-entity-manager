<?php

declare(strict_types=1);

use Medas\EntityManager\EntityManager;

// This file should be in the global namespace
function em(): EntityManager
{
    return medas()->serviceManager()->resolve(EntityManager::class);
}
