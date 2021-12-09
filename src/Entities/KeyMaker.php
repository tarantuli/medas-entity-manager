<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class KeyMaker
{
    public function get(string $className, array $id): string
    {
        return $className . ':' . json_encode($id);
    }
}
