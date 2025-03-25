<?php

declare(strict_types=1);

namespace Medas\EntityManager\MetaData;

use Medas\Core\{Attributes\Service, Interfaces\ConfigOption, Interfaces\ConfigOptionController};
use Medas\EntityManager\{Attributes, Exceptions\NoConfigOptionControllerFound};

#[Service]
readonly class EntityCompiler
{
    public function __construct(
        private ConfigOptionController|null $configOptionController,
    )
    {
    }

    public function compile(\ReflectionClass $class): Attributes\Entity|null
    {
        if (!$entity = attribute(Attributes\Entity::class, $class)) {
            return null;
        }

        if ($storeConfigOption = attribute(Attributes\Entity\StoreConfigOption::class, $class)) {
            if (!$this->configOptionController) {
                throw new NoConfigOptionControllerFound();
            }

            /** @var ConfigOption $option */
            $option = service($storeConfigOption->className);
            $entity->store = $this->configOptionController->getValue($option);
        }

        if ($storageConfigOption = attribute(Attributes\Entity\StorageConfigOption::class, $class)) {
            if (!$this->configOptionController) {
                throw new NoConfigOptionControllerFound();
            }

            /** @var ConfigOption $option */
            $option = service($storageConfigOption->className);
            $entity->storage = $this->configOptionController->getValue($option);
        }

        return $entity;
    }
}
