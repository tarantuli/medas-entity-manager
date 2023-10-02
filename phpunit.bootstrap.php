<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\Core\Interfaces\ConfigManager;
use Medas\EntityManager\EntityManagerPackage;
use Medas\EntityManagerTest\MockUps\MockUpPackage;
use Medas\Events\EventsPackage;
use Medas\FileSystem\FileSystemPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

new ServiceManager(
    function (): ServiceConfig {
        $config = new ServiceConfig();
        $config->addPackages([
            ConfigManagerPackage::instance(),
            ConfigOptionsPackage::instance(),
            EntityManagerPackage::instance(),
            EventsPackage::instance(),
            FileSystemPackage::instance(),
            MockUpPackage::instance(),
        ]);

        return $config;
    }
);

service(ConfigManager::class)
    ->addDirectory(__DIR__ . '/tests/MockUps/config');
