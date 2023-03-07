<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\ServiceManager\Attributes\{PreferredClass, Service};
use Medas\ServiceManager\Interfaces\Serializer;

#[Service]
class KeyMaker
{
    #[PreferredClass(Serializer::class, IdSerializer::class)]
    public function __construct(
        private readonly Serializer $idSerializer,
    )
    {
    }

    public function get(string $className, mixed $id): string
    {
        return $className . ':' . $this->idSerializer->serialize($id);
    }
}
