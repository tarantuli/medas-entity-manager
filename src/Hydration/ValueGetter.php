<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\EntityManager\MetaData;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ValueGetter
{
    /** @param MetaData\Property[] $fields */
    public function getValues(object $entity, array $fields): array
    {
        $values = [];

        foreach ($fields as $field) {
            if ($field->reflection->isInitialized($entity)) {
                $values[$field->name] = $field->reflection->getValue($entity);
            }
        }

        return $values;
    }
}
