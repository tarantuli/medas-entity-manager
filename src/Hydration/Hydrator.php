<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Entities\{
    ReferenceInitializer,
    ValueFetchers\EntityValueFetchersManager,
    ValueFetchers\OriginalClassFetcherManager
};
use Medas\EntityManager\EntityManager;
use Medas\EntityManager\Exceptions\OriginalClassNotFound;
use Medas\EntityManager\MetaData;

#[Service]
readonly class Hydrator
{
    public function __construct(
        private EntityValueFetchersManager  $entityValueFetchersManager,
        private OriginalClassFetcherManager $originalClassFetcherManager,
        private ReferenceInitializer        $referenceInitializer,
        private ValueSetter                 $valueSetter,
    )
    {
    }

    public function hydrate(MetaData $metaData, object $entity, EntityManager $entityManager): void
    {
        foreach ($metaData->properties as $property) {
            if ($property->isId) {
                continue;
            }

            foreach ($this->entityValueFetchersManager->get() as $entityValueFetcher) {
                $fetchResult = $entityValueFetcher->fetch($metaData, $entity, $property);

                if ($fetchResult->foundValue) {
                    $this->valueSetter->set(
                        $metaData,
                        $entity,
                        $property->name,
                        $fetchResult->value
                    );

                    break;
                }
            }
        }

        $this->referenceInitializer->initialize($entity, $entityManager);
    }

    public function fetchOriginalClass(MetaData $metaData, mixed $id): string
    {
        foreach ($this->originalClassFetcherManager->get() as $originalClassFetcher) {
            $result = $originalClassFetcher->fetch($metaData, $id);

            if ($result->foundValue) {
                return $result->value;
            }
        }

        throw new OriginalClassNotFound($metaData->className, $id);
    }
}
