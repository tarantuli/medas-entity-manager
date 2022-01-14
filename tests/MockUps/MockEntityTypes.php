<?php

declare(strict_types=1);

namespace Medas\Test\MockUps;

use Medas\EntityManager\Attributes\Entity;
use Medas\EntityManager\Attributes\Id;
use Medas\EntityManager\Attributes\Property;
use Medas\EntityManager\Types\Binary;

#[Entity]
class MockEntityTypes
{
    #[Id]
    private int $id;

    #[Property]
    private string $implicitType;

    #[Property, Binary]
    private string $explicitType;

    #[Property]
    private MockEntity $relation;

    #[Property]
    private $noPhpType;

    #[Property]
    private mixed $mixedType;

    #[Property]
    private string|int $unionType;

    #[Property]
    private string|null $pipeNullableType;

    #[Property]
    private ?string $questionMarkNullableType;
}
