<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

interface HasId
{
    public function id(): mixed;
}
