<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

use Medas\EntityManager\Storage\Interfaces\Action;
use Medas\EntityManager\Storage\Interfaces\Store;

class Table implements Store
{
    public function __construct(
        public Database $database,
        public string   $name,
    )
    {
    }

    public function getRecord(array $filters): Record
    {
        $this->database->execute($this->prepareGet($filters));

        return new Record($this->database->lastStatement()->fetch());
    }

    public function prepareGet(array $filters): Action
    {
        return $this->database->actionBuilder()->select([$this], $filters);
    }

    public function prepareCreate(array $values): Action
    {
        return $this->database->actionBuilder()->create($this, $values);
    }

    public function prepareUpdate(array $updates, array $conditions): Action
    {
        return $this->database->actionBuilder()->update($this, $updates, $conditions);
    }

    public function getCreateTable(): string
    {
        $this->database->actionBuilder()->showCreate($this)->execute();
        return $this->database->lastStatement()->fetchColumn(1);
    }
}
