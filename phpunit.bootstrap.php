<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManager;
use Medas\EntityManager\EntityManagerPackage;
use Medas\ServiceManager\ServiceManager;

$sm = ServiceManager::get();

$sm->addPackage(EntityManagerPackage::instance());

/** @var ConfigManager $config */
$config = $sm->resolve(ConfigManager::class);
$config->readEnv(__DIR__);
$config->addDirectory(__DIR__ . '/config');
