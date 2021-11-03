<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

use Medas\EntityManager\Attributes\Interfaces\Type;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
abstract class Stored implements Type
{
}
