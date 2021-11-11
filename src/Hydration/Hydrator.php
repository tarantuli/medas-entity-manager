<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\EntityManager\MetaData;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Hydrator
{

    public function hydrate(object $entity, MetaData $metaData): void
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
}
