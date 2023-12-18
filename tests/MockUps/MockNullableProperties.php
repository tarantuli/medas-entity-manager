<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\EntityManager\{Attributes\Entity, Attributes\Id, Types\Text};

#[Entity]
class MockNullableProperties
{
    #[Id]
    private int $id;

    private string $stringType;

    #[Text]
    private mixed $mixedType;

    #[Text]
    private string|int $unionType;

    private string|null $pipeNullableType;
    private string|null $questionMarkNullableType;
}
