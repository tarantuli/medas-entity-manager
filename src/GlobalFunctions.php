<?php

declare(strict_types=1);

// This file should be in the global namespace

use Medas\EntityManager\DatabaseManager;
use Medas\EntityManager\Storage\Interfaces\Database;
use Medas\ServiceManager\ServiceManager;

function db(): Database
{
    static $dm;

    if (!isset($dm)) {
        $dm = ServiceManager::get()->resolve(DatabaseManager::class);
    }

    return $dm->get();
}
