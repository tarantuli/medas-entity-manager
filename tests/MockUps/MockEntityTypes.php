<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\EntityManager\Attributes\{Entity, Id, Property};
use Medas\EntityManager\Types\Binary;
use Medas\Core\Interfaces\Guid;

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

    #[Property]
    private \DateTime $dateTime;

    #[Property]
    private Guid $guid;
}
