<?php

declare(strict_types=1);

use Medas\EntityManager\DatabaseManager;
use Medas\EntityManager\EntityManager;
use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\EnvManager\EnvManager;
use Medas\ServiceManager\ServiceManager;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Contracts\Cache\CacheInterface;

$sm = ServiceManager::get();
$sm->addPackages([
    EntityManager::class,
    EnvManager::class,
]);

$cache = new ApcuAdapter('entity-manager');
$cache->clear();
$sm->bindService($cache, CacheInterface::class);

$env = $sm->resolve(EnvManager::class);
$env->setRoot(__DIR__);
$env->setEnv('test');

$dm = $sm->resolve(DatabaseManager::class);
$dm->add($sm->resolve(Database::class));
