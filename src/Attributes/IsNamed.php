<?php

declare(strict_types=1);

namespace Medas\EntityManager\Attributes;

interface IsNamed
{
    public function name(): string;
}
