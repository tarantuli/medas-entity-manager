<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\Core\{
    CompiledValue,
    Interfaces\CompilableArgumentProcessor,
    Interfaces\EntityManager as EntityManagerInterface,
    ProcessesByCompiling
};

// Turns a scalar id into the entity it identifies. When a parameter is typed as
// an entity class but the resolved argument is a scalar - e.g. a #[ConfigValue]
// that produced an id string - the scalar can only mean one thing there, an id,
// since a bare scalar is otherwise an invalid value for an entity-typed
// parameter. So it's fetched and replaced by the instance.
//
// Runs after resolution as an argument processor (not a parameter resolver), so
// it composes with whatever produced the id - config, a given argument, another
// resolver - rather than owning resolution itself.
readonly class EntityById implements CompilableArgumentProcessor
{
    use ProcessesByCompiling;

    public function priority(): int
    {
        return -50;
    }

    // Only parameters typed as an entity are compiled; for all others this
    // processor never changes the argument.
    public function compile(\ReflectionParameter|\ReflectionProperty $parameter): CompiledValue|null
    {
        $entityClass = $this->entityType($parameter);

        return $entityClass === null ? null : new CompiledValue($entityClass);
    }

    // The first entity-typed declaration on the parameter, or null when it isn't
    // typed as an entity. Builtins are filtered before any reflection on the
    // class.
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

    /**
     * @param CompiledValue $compiled
     */
    public function processCompiled(object $compiled, mixed $argument): mixed
    {
        // Only a scalar can stand in for an entity as its id; leave nulls,
        // already-hydrated objects and everything else untouched.
        if (!is_scalar($argument)) {
            return $argument;
        }

        $entityClass = $compiled->value;

        // The entity decides its own id type; IdCaster coerces the scalar to it.
        return service(EntityManagerInterface::class)->get(
            $entityClass,
            service(Entities\IdCaster::class)->cast($entityClass, $argument)
        );
    }
}
