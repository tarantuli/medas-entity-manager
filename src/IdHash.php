<?php

declare(strict_types=1);

namespace Medas\EntityManager;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class IdHash
{
    public function get(array $id): string
    {
        return json_encode($id);
    }
}
