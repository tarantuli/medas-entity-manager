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
        // Properties are accessible by default starting with PHP version 8.1
        if (version_compare(PHP_VERSION, '8.1') >= 0) {
            return;
        }

        if (array_key_exists($metaData->getClassName(), $this->processedClasses)) {
            return;
        }

        foreach ($metaData->getProperties() as $property) {
            $property->setAccessible(true);
        }

        $this->processedClasses[$metaData->getClassName()] = true;

    }
}
