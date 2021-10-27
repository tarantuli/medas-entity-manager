<?php

declare(strict_types=1);

use Medas\EntityManager\DatabaseManager;
use Medas\EntityManager\Storage\Databases\Pdo\Database as PdoDatabase;
use Medas\ServiceManager\ServiceManager;

$sm = ServiceManager::get();
$sm->addSourceDirectory(realpath(__DIR__ . '/src'));

$dm = $sm->resolve(DatabaseManager::class);

$dm->add(new PdoDatabase(new \PDO('mysql:dbname=medas_test;host=127.0.0.1', 'root', 'kaas')));
