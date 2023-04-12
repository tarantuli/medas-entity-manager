<?php

declare(strict_types=1);

namespace Medas\EntityManager\Properties;

use Medas\EntityManager\Attributes\Property;
use Medas\EntityManager\Exceptions\InvalidHandler;
use Medas\ServiceManager\Exceptions\ServiceNotFoundByType;
use Medas\ServiceManager\Service;

#[Service]
class PropertyManager
{
    public function getHandler(\ReflectionProperty $property): Handler|null
    {
        $attribute = attribute(Property::class, $property);

        if ($attribute === null || !isset($attribute->handler)) {
            return null;
        }

        try {
            $handler = service($attribute->handler);
        }
        catch (ServiceNotFoundByType) {
            throw new InvalidHandler($attribute->handler);
        }

        if (!$handler instanceof Handler) {
            throw new InvalidHandler($attribute->handler);
        }

        return $handler;
    }
}
