<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class PropertyAccessManager
{
    public function makeAccessible(MetaData $metaData): void
    {
        // Properties are accessible by default starting with PHP version 8.1
        if (version_compare(PHP_VERSION, '8.1') >= 0) {
            return;
        }

        foreach ($metaData->properties as $property) {
            $property->reflection->setAccessible(true);
        }
    }
}
