<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo;

class Table implements \Medas\ServiceManager\Interfaces\Storage\Table
{
    public function __construct(private Database $database, private string $name)
    {
    }

    public function getByValues(array $values): RecordCollection
    {
        [$query, $parameters] = $this->database->queries()->getByValues($this->name, $values);
        $stmt = $this->database->pdo()->prepare($query);
        $stmt->execute($parameters);

        return new RecordCollection($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }
}
