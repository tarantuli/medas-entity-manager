<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\Attributes\Service;

#[Service]
readonly class EntityClassFinder
{
    public function __construct(
        private EntityClasses   $entityClasses,
        private MetaDataManager $metaDataManager,
    )
    {
    }

    public function getByStore(string $store, string|null $storage = null): MetaData|null
    {
        foreach ($this->entityClasses->get() as $className) {
            $metaData = $this->metaDataManager->get($className);

            if ($metaData->entity->store === $store && $metaData->entity->storage === $storage) {
                return $metaData;
            }
        }

        return null;
    }
}
