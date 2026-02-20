<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator\NameConverters;

use Medas\Core\{Attributes\Service, Identifier};
use Medas\EntityManager\Entities\Generator\Pluralizers\Pluralizer;

#[Service]
readonly class SnakeCaseNames implements NameConverter
{
    public function __construct(
        private Pluralizer $pluralizer,
    )
    {
    }

    public function convert(string $name): string
    {
        return new Identifier($this->pluralizer->pluralize($name))->toSnakeCase();
    }
}
