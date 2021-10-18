<?php

declare(strict_types=1);

namespace EntityManager;

use EntityManager\Attributes\Id;

class MetaData
{
    private static array $instances = [];

    public static function forClass(string $className)
    {
        if (!array_key_exists($className, self::$instances)) {
            self::$instances[$className] = new self($className);
        }

        return self::$instances[$className];
    }

    private \ReflectionClass $class;

    /**
     * @var array<string, \ReflectionProperty>
     */
    private array $idProperties;

    private function __construct(private string $className)
    {
        $this->class = new \ReflectionClass($this->className);
    }

    public function getIdProperties(): array
    {
        if (!isset($this->idProperties)) {
            $this->determineIdProperties();
        }

        return $this->idProperties;
    }

    private function determineIdProperties(): void
    {
        $this->idProperties = [];
        foreach ($this->class->getProperties() as $property) {
            if ($property->getAttributes(Id::class)) {
                $this->idProperties[$property->name] = $property;
            }
        }

        ksort($this->idProperties);
    }

    public function getIdPropertyNames(): array
    {
        if (!isset($this->idProperties)) {
            $this->determineIdProperties();
        }

        return array_keys($this->idProperties);
    }

    public function getClassName(): string
    {
        $this->className;
    }
}
