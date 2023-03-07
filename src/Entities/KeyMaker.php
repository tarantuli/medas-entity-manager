<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\EntityManager\Attributes\HasId;
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Interfaces\Guid;

#[Service]
class KeyMaker
{
    public function get(string $className, array $values): string
    {
        foreach ($values as &$value) {
            if ($value instanceof HasId) {
                $value = $value->id();
            }

            if ($value instanceof Guid) {
                $value = $value->toBytes();
            }
        }

        return $className . ':' . serialize($values);
    }
}
