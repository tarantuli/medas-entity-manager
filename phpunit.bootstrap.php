<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\Core\Interfaces\ConfigManager;
use Medas\EntityManager\EntityManagerPackage;
use Medas\EntityManagerTest\MockUps\MockUpPackage;
use Medas\Events\EventsPackage;
use Medas\ObjectInstantiator\ObjectInstantiator;
use Medas\ServiceManager\{ServiceConfigBuilder, ServiceManager};

chdir(__DIR__);

new ServiceManager(function (): ServiceConfigBuilder {
    $config = new ServiceConfigBuilder(ObjectInstantiator::class);

    $config->addPackages([
        ConfigManagerPackage::instance(),
        ConfigOptionsPackage::instance(),
        EntityManagerPackage::instance(),
        EventsPackage::instance(),
        MockUpPackage::instance(),
    ]);

    return $config;
});

service(ConfigManager::class)
    ->addDirectory(__DIR__ . '/tests/MockUps/config');
