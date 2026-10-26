<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\{Attributes\Service, Interfaces\IsLazyLoaded, Interfaces\TracksChanges};
use Medas\EntityManager\MetaDataManager;

/**
 * Gives a new entity an empty collection for every stored collection property
 * (#[EntityCollection]) that nothing has set, so a new entity is as complete as a
 * loaded one: a create that sends no value for a collection holds an empty one,
 * not an uninitialised property that a reader or a normaliser trips over.
 *
 * The counterpart of ReferenceInitializer, which does the same for
 * back-references. Called by EntityManager::create() and persist(); a property
 * that is already set, as on a loaded entity, is left alone.
 */
#[Service]
readonly class CollectionInitializer
{
    public function __construct(
        private MetaDataManager $metaDataManager,
    )
    {
    }

    public function initialize(object $entity): void
    {
        $metaData = $this->metaDataManager->get($entity::class);

        foreach ($metaData->properties as $property) {
            $collectionClass = $property->collectionClass;

            if ($collectionClass === null || $property->reflection->isInitialized($entity)) {
                continue;
            }

            $collection = new $collectionClass();

            // A lazy collection without a loader throws on first access
            if ($collection instanceof IsLazyLoaded) {
                $collection->setLoader(fn() => []);
            }

            // Empty from the start, so there is nothing for a flush to record as changed.
            if ($collection instanceof TracksChanges) {
                $collection->resetChangeTracking();
            }

            $property->reflection->setValue($entity, $collection);
        }
    }
}
