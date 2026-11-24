<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\PackageEntities;

use Medas\EntityManager\Attributes\{Entity, Id};

/**
 * An entity outside the configured entity directories, published by `MockPackageEntities`.
 */
#[Entity('package_entities')]
class PackageEntity
{
    #[Id]
    private int $id;
}
