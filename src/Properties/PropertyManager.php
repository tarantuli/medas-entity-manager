<?php

declare(strict_types=1);

namespace Medas\EntityManager\Properties;

use Medas\Core\{Attributes\Handler, Attributes\Service, Interfaces\PropertyHandler};
use Medas\EntityManager\Exceptions\InvalidHandler;
use Medas\ServiceManager\Exceptions\ServiceNotFoundByType;

#[Service]
readonly class PropertyManager
{
    public function getHandler(\ReflectionProperty $property): PropertyHandler|null
    {
        $attribute = attribute(Handler::class, $property);

        if ($attribute === null || !isset($attribute->className)) {
            return null;
        }

        try {
            $handler = service($attribute->className);
        }
        catch (ServiceNotFoundByType) {
            throw new InvalidHandler($attribute->className);
        }

        if (!$handler instanceof PropertyHandler) {
            throw new InvalidHandler($attribute->className);
        }

        return $handler;
    }
}
