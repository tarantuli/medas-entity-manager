<?php

declare(strict_types=1);

namespace Medas\EntityManager\Types;

use Medas\ServiceManager\Interfaces\Type;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
abstract class BaseType implements Type
{
}
