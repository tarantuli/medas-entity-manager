<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Structure;

use Medas\EntityManager\Storage\Databases\Pdo\Structure\Blueprint\Field;
use Medas\EntityManager\Storage\Databases\Pdo\Structure\Blueprint\Index;

class Changes
{
    /** @var Field[] */
    public array $addFields = [];
    /** @var Field[] */
    public array $changeFields = [];
    /** @var Index[] */
    public array $indexes = [];

    public function __construct(public string $name)
    {
    }
}
