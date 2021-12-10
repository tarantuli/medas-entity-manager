<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

use Medas\EntityManager\Storage\Databases\Pdo\Database;
use Medas\EntityManager\Storage\Databases\Pdo\Table;
use Medas\EntityManager\Storage\Interfaces\Store;

class BaseSqlQueryBuilder implements QueryBuilder
{
    private string $query;
    private array $arguments;

    public function __construct(private Database $storage)
    {
    }

    /** @param Table[] $stores */
    public function select(array $stores, array $filters): Query
    {
        $this->arguments = [];
        $this->query = 'select * from ';

        foreach ($stores as $table) {
            $this->query .= $table->name . ',';
        }

        $this->query = substr($this->query, 0, -1);

        if ($filters) {
            $this->query .= ' where ';
            $this->appendParameters($filters);
        }

        return new Query($this->query, $this->arguments, $this->storage);
    }

    private function appendParameters(array $filters, string $separator = 'and'): void
    {
        foreach ($filters as $field => $value) {
            $this->query .= $field . '=? ' . $separator . ' ';
            $this->arguments[] = $value;
        }

        $this->query = substr($this->query, 0, -2 - strlen($separator));
    }

    public function update(Store $store, array $updates, array $conditions): Query
    {
        $this->arguments = [];

        $this->query = 'update ' . $store->name . ' set ';
        $this->appendParameters($updates);

        $this->query .= ' where ';
        $this->appendParameters($conditions);

        return new Query($this->query, $this->arguments, $this->storage);
    }

    public function create(Store $store, array $values): Query
    {
        $this->arguments = [];

        $this->query = 'insert into ' . $store->name . ' set ';
        $this->appendParameters($values);

        return new Query($this->query, $this->arguments, $this->storage);
    }
}
