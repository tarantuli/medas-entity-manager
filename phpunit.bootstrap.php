<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\Core\Interfaces\ConfigManager;
use Medas\EntityManager\EntityManagerPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

new ServiceManager(
    function (): ServiceConfig {
        $config = new ServiceConfig();
        $config->addPackages([
            EntityManagerPackage::instance(),
            ConfigManagerPackage::instance(),
            ConfigOptionsPackage::instance(),
        ]);

        return $config;
    }
);

service(ConfigManager::class)
    ->addDirectory(__DIR__ . '/tests/MockUps/config');
