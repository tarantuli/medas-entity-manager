<?php

declare(strict_types=1);

use Medas\Cache\FilesystemCache;
use Medas\ConfigManager\ConfigManager;
use Medas\EntityManager\EntityManagerPackage;
use Medas\ServiceManager\ServiceManager;

$sm = ServiceManager::get();

$cache = new FilesystemCache(__DIR__ . '/var/cache');
$cache->clear();
$sm->setCache($cache);

$sm->addPackage(EntityManagerPackage::instance());

/** @var ConfigManager $config */
$config = $sm->resolve(ConfigManager::class);
$config->readEnv(__DIR__);
$config->addDirectory(__DIR__ . '/config');
