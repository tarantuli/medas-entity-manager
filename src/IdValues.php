<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Exceptions\MissingIdValueException;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class IdValues
{
    public function get(array $idValues, MetaData $metaData): array
    {
        $idProperties = $metaData->idProperties;
        $values = [];

        foreach ($idProperties as $idProperty) {
            if (!array_key_exists($idProperty->name, $idValues)) {
                throw new MissingIdValueException($idProperty->name);
            }

            $values[$idProperty->name] = $idValues[$idProperty->name];
        }
        return $values;
    }
}
