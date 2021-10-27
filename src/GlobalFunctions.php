<?php

declare(strict_types=1);

// This file should be in the global namespace

use Medas\EntityManager\Storage\Interfaces\Database;

function db(): Database
{
    return $GLOBALS['DatabaseManager']->get();
}
