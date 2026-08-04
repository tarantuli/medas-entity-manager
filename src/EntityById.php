<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{
    Exceptions\ServiceNotFoundByType,
    Exceptions\UuidProviderIsNotAvailable,
    Interfaces\ArgumentProcessor,
    Interfaces\EntityManager as EntityManagerInterface,
    Interfaces\UuidProvider
};

// Turns a scalar id into the entity it identifies. When a parameter is typed as
// an entity class but the resolved argument is a scalar - e.g., a #[ConfigValue]
// that produced an id string - the scalar can only mean one thing there, an id,
// since a bare scalar is otherwise an invalid value for an entity-typed
// parameter. So it's fetched and replaced by the instance.
//
// Runs after resolution as an argument processor (not a parameter resolver), so
// it composes with whatever produced the id - config, a given argument, another
// resolver - rather than owning resolution itself.
readonly class EntityById implements ArgumentProcessor
{
    public function priority(): int
    {
        return -50;
    }

    public function process(\ReflectionParameter|\ReflectionProperty $parameter, mixed $argument): mixed
    {
        // Only a scalar can stand in for an entity as its id; leave nulls,
        // already-hydrated objects and everything else untouched.
        if (!is_scalar($argument)) {
            return $argument;
        }

        $entityClass = $this->entityType($parameter);

        if ($entityClass === null) {
            return $argument;
        }

        return service(EntityManagerInterface::class)->get($entityClass, $this->toId($argument));
    }

    // The first entity-typed declaration on the parameter, or null when it isn't
    // typed as an entity. Builtins are filtered before any reflection on the
    // class, so this stays cheap for the overwhelming majority of arguments
    // (strings, ints, ...), whose types are never entities.
    private function entityType(\ReflectionParameter|\ReflectionProperty $parameter): string|null
    {
        $types = $parameter instanceof \ReflectionProperty
            ? propertyTypes($parameter)
            : parameterTypes($parameter);

        foreach ($types as $type) {
            if ($type->isBuiltin()) {
                continue;
            }

            $name = $type->getName();

            if (class_exists($name) && new \ReflectionClass($name)->getAttributes(Attributes\Entity::class) !== []) {
                return $name;
            }
        }

        return null;
    }

    private function toId(int|float|string|bool $argument): mixed
    {
        // Entities are keyed by a Uuid id, so a string id (as config and request
        // input deliver it) has to become the Uuid the entity manager looks up
        // by. A non-string scalar is passed through unchanged for the manager to
        // key on or reject.
        if (!is_string($argument)) {
            return $argument;
        }

        try {
            $uuidProvider = service(UuidProvider::class);
        }
        catch (ServiceNotFoundByType) {
            throw new UuidProviderIsNotAvailable();
        }

        return $uuidProvider->fromString($argument);
    }
}
