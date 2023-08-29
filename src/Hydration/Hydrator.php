<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Entities\{EntityValueFetcher, IdValue, SelectorRecordsFetcher};
use Medas\EntityManager\Events\MustClearEntityValueCaches;
use Medas\EntityManager\MetaData;
use Medas\EntityManager\MetaDataManager;
use Medas\EntityManager\Selector\Selectors\WithValues;
use Medas\Events\Interfaces\Listener;

#[Service]
class Hydrator implements Listener
{
    public function __construct(
        private readonly IdValue            $idValue,
        private EntityValueFetcher|null     $entityValueFetcher,
        private SelectorRecordsFetcher|null $selectorRecordsFetcher,
        private readonly MetaDataManager    $metaDataManager,
        private readonly ValueGetter        $valueGetter,
        private readonly ValueSetter        $valueSetter,
    )
    {
    }

    public function setEntityValueFetcher(EntityValueFetcher|null $entityValueFetcher): void
    {
        $this->entityValueFetcher = $entityValueFetcher;
    }

    public function setSelectorRecordsFetcher(?SelectorRecordsFetcher $selectorRecordsFetcher): void
    {
        $this->selectorRecordsFetcher = $selectorRecordsFetcher;
    }

    public function hydrate(MetaData $metaData, object $entity): void
    {
        if (!$this->entityValueFetcher) {
            return;
        }

        foreach ($metaData->properties as $property) {
            $fetchResult = $this->entityValueFetcher->fetch($metaData, $entity, $property);

            if ($fetchResult->foundValue) {
                $this->valueSetter->set($metaData, $entity, $property->name, $fetchResult->value);
            }
        }

        foreach ($metaData->references as $reference) {
            $collectionClass = $metaData->property($reference->name)->phpTypes[0];
            $this->valueSetter->set(
                $metaData, $entity, $reference->name,
                new $collectionClass(fn() => $this->fetchReferences($entity, $reference)));
        }
    }

    private function fetchReferences(object $entity, MetaData\Reference $reference): array
    {
        $entities = [];
        $records = $this->selectorRecordsFetcher->fetch(new WithValues($reference->entity, [$reference->property => $entity->id]));
        $metaData = $this->metaDataManager->get($reference->entity);

        foreach ($records as $record) {
            $idValue = $this->idValue->get($record, $metaData);
            $entities[] = em()->get($metaData->className, $idValue);
        }

        return $entities;
    }

    public function setValues(MetaData $metaData, object $entity, array $values): void
    {
        $this->valueSetter->setValues($metaData, $entity, $values);
    }

    public function eventName(): string
    {
        return MustClearEntityValueCaches::class;
    }

    public function callable(): callable
    {
        return fn() => $this->entityValueFetcher->clearCaches();
    }
}
