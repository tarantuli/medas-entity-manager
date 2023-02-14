<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\EntityManager\EntityManagerPackage;
use Medas\ServiceManager\Interfaces\ConfigManager;
use Medas\ServiceManager\ServiceManager;

chdir(__DIR__);

$sm = ServiceManager::get();

$sm->addPackage(EntityManagerPackage::instance());
$sm->addPackage(ConfigManagerPackage::instance());
$sm->addPackage(ConfigOptionsPackage::instance());

$config = service(ConfigManager::class);
$config->addDirectory(__DIR__ . '/tests/MockUps/config');
