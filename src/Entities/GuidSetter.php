<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\{Attributes\Service, Exceptions\GuidProviderIsNotAvailable, Interfaces\GuidProvider};
use Medas\EntityManager\{MetaDataManager, Types\Guid};

#[Service]
readonly class GuidSetter
{
    public function __construct(
        private GuidProvider|null $guidProvider,
        private MetaDataManager   $metaDataManager,
    )
    {
    }

    /** @param object[] $entities */
    public function processEntities(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->processEntity($entity);
        }
    }

    public function processEntity(object $entity): void
    {
        $metaData = $this->metaDataManager->get($entity::class);

        foreach ($metaData->properties as $property) {
            if (!$property->type instanceof Guid) {
                continue;
            }

            if ($property->reflection->isInitialized($entity)) {
                continue;
            }

            if ($this->guidProvider === null) {
                throw new GuidProviderIsNotAvailable();
            }

            $property->reflection->setValue($entity, $this->guidProvider->create());
        }
    }
}
