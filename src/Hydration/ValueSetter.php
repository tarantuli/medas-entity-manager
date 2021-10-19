<?php

declare(strict_types=1);

namespace Medas\EntityManager\Hydration;

use Medas\EntityManager\MetaData;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ValueSetter
{
    public function setValues(MetaData $metaData, object $entity, array $values): void
    {
        foreach ($values as $propertyName => $value) {
            $this->setValue($metaData, $entity, $propertyName, $value);
        }
    }

    public function setValue(MetaData $metaData, object $entity, string $propertyName, mixed $value): void
    {
        $metaData->getProperty($propertyName)->setValue($entity, $value);
    }
}
