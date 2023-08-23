<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

use Medas\Core\Attributes\{PreferredDefault, Service};
use Medas\Core\Interfaces\Serializer;

#[Service]
readonly class KeyMaker
{
    public function __construct(
        #[PreferredDefault(IdSerializer::class)]
        private Serializer $idSerializer,
    )
    {
    }

    public function get(string $className, mixed $id): string
    {
        return $className . ':' . $this->idSerializer->serialize($id);
    }
}
