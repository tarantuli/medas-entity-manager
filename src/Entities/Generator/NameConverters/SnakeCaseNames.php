<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator\NameConverters;

use Medas\Core\{Attributes\Service, IdentifierMaker};
use Medas\EntityManager\Entities\Generator\Pluralizers\Pluralizer;

#[Service]
readonly class SnakeCaseNames implements NameConverter
{
    public function __construct(
        private IdentifierMaker $identifierMaker,
        private Pluralizer      $pluralizer,
    )
    {
    }

    public function convert(string $name): string
    {
        return $this->identifierMaker->fromCamelCase($this->pluralizer->pluralize($name))->toSnakeCase();
    }
}
