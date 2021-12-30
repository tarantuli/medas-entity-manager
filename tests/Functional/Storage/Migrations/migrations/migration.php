<?php

namespace Medas\Migrations;

use Medas\EntityManager\Storage\Databases\Pdo\Queries\Query;
use Medas\EntityManager\Storage\Migrations\Migration;
use Medas\EntityManager\Storage\UnitOfWork\UnitOfWork;

class Migration20211230123813 implements Migration
{
    public function migrate(UnitOfWork $unitOfWork): void
    {
        $unitOfWork->addAction(new Query("CREATE TABLE `new_stored_entities` (
 `id` int unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 `createdAt` datetime DEFAULT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `name` (`name`)
)
", [], storage("default")));
    }

    public function undo(UnitOfWork $unitOfWork): void
    {
    }
}
