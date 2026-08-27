<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\{
    Attributes\Service,
    Exceptions\UuidProviderIsNotAvailable,
    Interfaces\UuidProvider,
    Types\Uuid
};
use Medas\EntityManager\MetaDataManager;

#[Service]
readonly class UuidInitializer
{
    public function __construct(
        private MetaDataManager   $metaDataManager,
        private UuidProvider|null $uuidProvider,
    )
    {
    }

    /** @param object[] $entities */
    public function initializeAll(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->initialize($entity);
        }
    }

    public function initialize(object $entity): void
    {
        $metaData = $this->metaDataManager->get($entity::class);

        foreach ($metaData->properties as $property) {
            if (!$property->type instanceof Uuid) {
                continue;
            }

            if ($property->reflection->isInitialized($entity)) {
                continue;
            }

            if ($this->uuidProvider === null) {
                throw new UuidProviderIsNotAvailable();
            }

            $property->reflection->setValue($entity, $this->uuidProvider->create());
        }
    }
}
