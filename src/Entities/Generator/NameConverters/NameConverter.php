<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator\NameConverters;

interface NameConverter
{
    public function convert(string $name): string;
}
