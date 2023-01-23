<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\ServiceManager\{AsSingleton, BasePackage, ConfigOptions\ConfigOption};
use Medas\ConfigOptions\ConfigOptionsPackage;

class EntityManagerPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }

    public function initialize(): void
    {
        require_once __DIR__ . '/GlobalFunctions.php';

        parent::initialize();
    }
}
