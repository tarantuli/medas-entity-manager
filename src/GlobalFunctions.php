<?php

declare(strict_types=1);

// This file should be in the global namespace

use Medas\EntityManager\StorageManager;
use Medas\EntityManager\EntityManager;
use Medas\EntityManager\Storage\Interfaces\Storage;
use Medas\ServiceManager\ServiceManager;

function db(string $name = null): Storage
{
    /** @var StorageManager $dm */
    static $dm;

    if (!isset($dm)) {
        $dm = ServiceManager::get()->resolve(StorageManager::class);
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
