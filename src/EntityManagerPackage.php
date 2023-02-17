<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\FileSystem\FileSystemPackage;
use Medas\ServiceManager\{AsSingleton, BasePackage, ServiceConfig};

class EntityManagerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            FileSystemPackage::class,
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }

    public function initialize(ServiceConfig $config): void
    {
        require_once __DIR__ . '/GlobalFunctions.php';

        parent::initialize($config);
    }
}
