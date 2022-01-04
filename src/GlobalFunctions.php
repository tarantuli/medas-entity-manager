<?php

declare(strict_types=1);

// This file should be in the global namespace

use Medas\EntityManager\EntityManager;
use Medas\ServiceManager\ServiceManager;

function em(): EntityManager
{
    /** @var EntityManager $em */
    static $em;

    if (!isset($em)) {
        $em = ServiceManager::get()->resolve((EntityManager::class));
    }

    return $em;
}
