<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities;

enum State: int
{
    case Inactive = 0;
    case Active = 1;
}
