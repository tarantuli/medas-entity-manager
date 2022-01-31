<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

interface HasName
{
    public function name(): string;
}
