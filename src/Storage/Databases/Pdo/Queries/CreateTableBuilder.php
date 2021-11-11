<?php

declare(strict_types=1);

namespace Medas\EntityManager\Storage\Databases\Pdo\Queries;

use Medas\EntityManager\Attributes\Interfaces\Type;
use Medas\EntityManager\Attributes\Types\Integer;
use Medas\EntityManager\Storage\TableBlueprint;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class CreateTableBuilder
{
    public function fromBlueprint(TableBlueprint $blueprint): Query
    {
        $query = 'create table ' . $blueprint->name() . ' (';
        foreach ($blueprint->fields() as $name => $type) {
            $query .= sprintf('%s %s,', $name, $this->typeToDefinition($type));
        }

        $query = substr($query, 0, -1) . ')';
        $query .= ' engine=InnoDB';

        return new Query($query);
    }

    private function typeToDefinition(Type $type): string
    {
        return match (true) {
            $type instanceof Integer => $this->integerDefinition($type),
            default => throw new \Exception()
        };
    }

    private function integerDefinition(Integer $type): string
    {
        if ($type->minValue >= 0 && $type->maxValue <= Integer::UNSIGNED_1_BYTE_MAX) {
            return 'tinyint unsigned';
        }
        if ($type->minValue >= 0 && $type->maxValue <= Integer::UNSIGNED_2_BYTE_MAX) {
            return 'smallint unsigned';
        }
        if ($type->minValue >= 0 && $type->maxValue <= Integer::UNSIGNED_3_BYTE_MAX) {
            return 'mediumint unsigned';
        }
        if ($type->minValue >= 0 && $type->maxValue <= Integer::UNSIGNED_4_BYTE_MAX) {
            return 'int unsigned';
        }
        if ($type->minValue >= 0 && $type->maxValue <= Integer::UNSIGNED_8_BYTE_MAX) {
            return 'bigint unsigned';
        }

        diedump($type);
    }
}
