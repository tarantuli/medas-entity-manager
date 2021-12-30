<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Migrations;

use Medas\EntityManager\Storage\UnitOfWork\UnitOfWork;

interface Migration
{
    public function migrate(UnitOfWork $unitOfWork): void;

    public function undo(UnitOfWork $unitOfWork): void;
}
