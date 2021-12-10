<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManager;
use Medas\EntityManager\DatabaseManager;
use Medas\EntityManager\EntityManager;
use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\ServiceManager\ServiceManager;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Contracts\Cache\CacheInterface;

$sm = ServiceManager::get();
$sm->addPackages([
    EntityManager::class,
    ConfigManager::class,
]);

$cache = new ApcuAdapter('entity-manager');
$cache->clear();
$sm->bindService($cache, CacheInterface::class);

/** @var ConfigManager $config */
$config = $sm->resolve(ConfigManager::class);
$config->readEnv(__DIR__);
$config->addDirectory(__DIR__ . '/config');

/** @var DatabaseManager $dm */
$dm = $sm->resolve(DatabaseManager::class);
$dm->add($sm->instantiate(Database::class));

$sqliteFile = sys_get_temp_dir() . '/entity-manager-test.sqlite3';
if (file_exists($sqliteFile)) {
    unlink($sqliteFile);
}

$dm->add(new Database('sqlite:' . $sqliteFile, '', ''), 'sqlite3');
