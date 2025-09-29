<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\Core\Interfaces\Uuid;
use Medas\Core\Types\Binary;
use Medas\EntityManager\{Attributes\Entity,
    Attributes\Id,
    Attributes\Relations\Action,
    Attributes\Relations\OnDelete};

#[Entity]
class MockEntityTypes
{
    #[Id]
    private int $id;

    private string $implicitType;

    #[Binary]
    private string $explicitType;

    private MockEntity $relation;
    #[OnDelete(Action::Cascade)]
    private MockEntity $onCascadeDelete;
    private $noPhpType;
    private mixed $mixedType;
    private string|int $unionType;
    private string|null $pipeNullableType;
    private ?string $questionMarkNullableType;
    private \DateTime $dateTime;
    private Uuid $uuid;
    private MockEnum $enum;
}
