<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

use Medas\Core\Interfaces\Type;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
abstract class BaseType implements Type
{
}
