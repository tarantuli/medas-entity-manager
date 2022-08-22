<?php

declare(strict_types=1);

use Medas\EntityManager\EntityManagerPackage;
use Medas\ServiceManager\ServiceManager;

$sm = ServiceManager::get();

$sm->addPackage(EntityManagerPackage::instance());
