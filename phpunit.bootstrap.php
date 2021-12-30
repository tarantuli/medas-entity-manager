<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManager;
use Medas\EntityManager\EntityManagerPackage;
use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\EntityManager\StorageManager;
use Medas\ServiceManager\ServiceManager;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Contracts\Cache\CacheInterface;

$sm = ServiceManager::get();
$sm->addPackage(new EntityManagerPackage());
$cache = new ApcuAdapter('entity-manager');
$cache->clear();
$sm->bindService($cache, CacheInterface::class);

/** @var ConfigManager $config */
$config = $sm->resolve(ConfigManager::class);
$config->readEnv(__DIR__);
$config->addDirectory(__DIR__ . '/config');

/** @var StorageManager $storageManager */
$storageManager = $sm->resolve(StorageManager::class);
$storageManager->add($sm->instantiate(Database::class));

$sqliteFile = sys_get_temp_dir() . '/entity-manager-test.sqlite3';
if (file_exists($sqliteFile)) {
    unlink($sqliteFile);
}

$storageManager->add(new Database('sqlite:' . $sqliteFile, '', ''), 'sqlite3');
