<?php

declare(strict_types=1);

// This file should be in the global namespace

use Medas\EntityManager\DatabaseManager;
use Medas\EntityManager\EntityManager;
use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\ServiceManager\ServiceManager;

function db(string $name = null): Database
{
    /** @var DatabaseManager $dm */
    static $dm;

    if (!isset($dm)) {
        $dm = ServiceManager::get()->resolve(DatabaseManager::class);
    }

    return $dm->get($name);
}

function em(): EntityManager
{
    /** @var EntityManager $em */
    static $em;

    if (!isset($em)) {
        $em = ServiceManager::get()->resolve((EntityManager::class));
    }

    return $em;
}
