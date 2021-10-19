<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class PropertyAccessibleMaker
{
    private array $processedClasses = [];

    public function makeAccessible(MetaData $metaData): void
    {
        if (array_key_exists($metaData->getClassName(), $this->processedClasses)) {
            return;
        }

        foreach ($metaData->getProperties() as $property) {
            $property->setAccessible(true);
        }

        $this->processedClasses[$metaData->getClassName()] = true;

    }
}
