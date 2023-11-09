<?php

declare(strict_types=1);

namespace Medas\EntityManager\Properties;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{Attributes\Handler as HandlerAttribute, Exceptions\InvalidHandler};
use Medas\ServiceManager\Exceptions\ServiceNotFoundByType;

#[Service]
readonly class PropertyManager
{
    public function getHandler(\ReflectionProperty $property): Handler|null
    {
        $attribute = attribute(HandlerAttribute::class, $property);

        if ($attribute === null || !isset($attribute->className)) {
            return null;
        }

        try {
            $handler = service($attribute->className);
        }
        catch (ServiceNotFoundByType) {
            throw new InvalidHandler($attribute->className);
        }

        if (!$handler instanceof Handler) {
            throw new InvalidHandler($attribute->className);
        }

        return $handler;
    }
}
