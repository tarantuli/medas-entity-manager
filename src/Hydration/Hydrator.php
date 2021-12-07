<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\EntityManager\IdValues;
use Medas\EntityManager\MetaData;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Hydrator
{
    public function __construct(
        private IdValues    $idValues,
        private ValueSetter $valueSetter,
    )
    {
    }

    public function hydrate(MetaData $metaData, object $entity): void
    {
        $data = db($metaData->entity->db)->getTable($metaData->entity->table)
            ->getRecord(filters: $this->getValues($entity, $metaData->idProperties));
        funcdump($data);
    }

    /** @param MetaData\Property[] $fields */
    private function getValues(object $entity, array $fields): array
    {
        $values = [];

        foreach ($fields as $field) {
            $values[$field->name] = $field->reflection->getValue($entity);
        }

        return $values;
    }

    public function setIdValues(MetaData $metaData, object $entity, mixed $id)
    {
        if ($metaData->hasCompositeId) {
            $idValues = $this->idValues->get($id, $metaData);
        }
        else {
            $idValues = [$metaData->idProperty->name => $id];
        }

        $this->valueSetter->setValues($metaData, $entity, $idValues);
    }
}
