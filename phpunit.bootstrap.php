<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ServiceManager\Interfaces\ConfigManager;

require_once __DIR__ . '/bootstrap.php';

sm()->addPackage(ConfigManagerPackage::instance());

$config = service(ConfigManager::class);
$config->readEnv(__DIR__);
$config->addDirectory(__DIR__ . '/config');
