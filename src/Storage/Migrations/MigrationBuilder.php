<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Migrations;

use Medas\FileBuilder\PhpClass\MethodDefinition;

interface MigrationBuilder
{
    public function build(string $className, MethodDefinition $migrateMethod, MethodDefinition $undoMethod): void;
}
