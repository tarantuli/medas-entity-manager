<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\Core\{Attributes\Service, Interfaces\PackageEntities};

#[Service]
readonly class MockPackageEntities implements PackageEntities
{
    public function directories(): array
    {
        return [
            dirname(__DIR__) . '/PackageEntities',
        ];
    }
}
