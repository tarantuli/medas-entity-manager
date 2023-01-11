<?php

declare(strict_types=1);

namespace Medas\Test\MockUps;

use Medas\EntityManager\Attributes\Entity;
use Medas\EntityManager\Attributes\Id;
use Medas\EntityManager\Attributes\Property;
use Medas\EntityManager\Types\Text;

#[Entity]
class MockNullableProperties
{
    #[Id]
    private int $id;

    #[Property]
    private string $stringType;

    #[Property, Text]
    private mixed $mixedType;

    #[Property, Text]
    private string|int $unionType;

    #[Property]
    private string|null $pipeNullableType;

    #[Property]
    private ?string $questionMarkNullableType;
}
