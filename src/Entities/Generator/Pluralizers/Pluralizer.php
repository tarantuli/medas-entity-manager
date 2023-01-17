<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator\Pluralizers;

interface Pluralizer
{
    public function pluralize(string $string): string;
}
