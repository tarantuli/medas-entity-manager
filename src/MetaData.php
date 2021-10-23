<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Attributes\Entity;
use Medas\EntityManager\Attributes\Id;
use Medas\EntityManager\Attributes\Stored;
use Medas\EntityManager\Exceptions\ClassIsNotAnEntityException;
use Medas\EntityManager\Exceptions\EntityHasNoIdPropertyException;
use Medas\EntityManager\Exceptions\PropertyDoesNotExistException;

class MetaData
{
    private static array $instances = [];

    public static function forClass(string $className): self
    {
        if (array_key_exists($className, self::$instances)) {
            return self::$instances[$className];
        }

        $class = new \ReflectionClass($className);

        if (!$class->getAttributes(Entity::class)) {
            throw new ClassIsNotAnEntityException($className);
        }

        return self::$instances[$className] = new self($className, $class);
    }

    private ?\ReflectionProperty $idProperty;

    /**
     * @var array<\ReflectionProperty>
     */
    private array $idProperties;

    /**
     * @var array<\ReflectionProperty>
     */
    private array $properties;

    private function __construct(private string $className, private \ReflectionClass $class)
    {
        $this->determineProperties();
        $this->determineIdProperties();
    }

    private function determineProperties()
    {
        foreach ($this->class->getProperties() as $property) {
            if ($property->getAttributes(Stored::class, \ReflectionAttribute::IS_INSTANCEOF)) {
                $this->properties[] = $property;
            }
        }
    }

    private function determineIdProperties(): void
    {
        $this->idProperties = [];

        foreach ($this->getProperties() as $property) {
            if ($property->getAttributes(Id::class)) {
                $this->idProperties[] = $property;
                $this->idProperty = $property;
            }
        }

        if (count($this->idProperties) === 0) {
            throw new EntityHasNoIdPropertyException($this->className);
        }

        if (count($this->idProperties) > 1) {
            $this->idProperty = null;
        }
    }

    /**
     * @return array<\ReflectionProperty>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function hasCompositeId(): bool
    {
        return $this->idProperty === null;
    }

    public function getIdProperty(): ?\ReflectionProperty
    {
        return $this->idProperty;
    }

    public function getIdProperties(): array
    {
        return $this->idProperties;
    }

    public function getProperty(string $propertyName): \ReflectionProperty
    {
        foreach ($this->properties as $property) {
            if ($property->name === $propertyName) {
                return $property;
            }
        }

        throw new PropertyDoesNotExistException($this->className, $propertyName);
    }
}
