<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class KeyMaker
{
    public function get(string $className, array $id): string
    {
        foreach ($id as &$value) {
            if (is_object($value)) {
                $value = spl_object_id($value);
            }
        }

        return $className . ':' . print_r($id, true);
    }
}
