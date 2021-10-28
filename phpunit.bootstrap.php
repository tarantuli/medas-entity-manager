<?php

declare(strict_types=1);

use Medas\EntityManager\DatabaseManager;
use Medas\EntityManager\Storage\Databases\Pdo\Database as PdoDatabase;
use Medas\EnvManager\EnvManager;
use Medas\ServiceManager\ServiceManager;

$sm = ServiceManager::get();
$sm->addSourceDirectory(realpath(__DIR__ . '/src'));
$sm->addSourceDirectory(realpath(__DIR__ . '/vendor/morphp/medas-env-manager/src'));

$env = $sm->resolve(EnvManager::class);
$env->setRoot(__DIR__);
$env->setEnv('test');

$dm = $sm->resolve(DatabaseManager::class);

$dm->add(new PdoDatabase(new PDO(
    env('db.dns'), env('db.username'), env('db.password')
)));
