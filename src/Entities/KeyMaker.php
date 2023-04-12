<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\Interfaces\Serializer;
use Medas\ServiceManager\Attributes\PreferredDefault;
use Medas\ServiceManager\Service;

#[Service]
class KeyMaker
{
    public function __construct(
        #[PreferredDefault(IdSerializer::class)]
        private readonly Serializer $idSerializer,
    )
    {
    }

    public function get(string $className, mixed $id): string
    {
        return $className . ':' . $this->idSerializer->serialize($id);
    }
}
