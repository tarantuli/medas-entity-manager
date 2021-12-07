<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\EntityManager\Exceptions\IdValueShouldBeAnArrayException;
use Medas\EntityManager\Exceptions\IdValueShouldBeAScalarException;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class IdHash
{
    public function __construct(
        private IdValues $idValues,
    )
    {
    }

    public function get(mixed $id, MetaData $metaData): string
    {
        if (!$metaData->hasCompositeId) {
            if (!is_scalar($id)) {
                throw new IdValueShouldBeAScalarException($metaData->className, gettype($id));
            }

            return (string) $id;
        }

        if (!is_array($id)) {
            throw new IdValueShouldBeAnArrayException($metaData->className, gettype($id));
        }

        return $this->getComplexIdHash($id, $metaData);
    }

    private function getComplexIdHash(array $idValues, MetaData $metaData): string
    {
        return json_encode($this->idValues->get($idValues, $metaData));
    }
}
