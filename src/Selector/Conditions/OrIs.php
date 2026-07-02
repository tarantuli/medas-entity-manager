<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Conditions;

use Medas\EntityManager\Exceptions\ConditionHasNoProperty;

class OrIs implements Condition
{
    /** @param Condition[] $conditions */
    public static function c(...$conditions): static
    {
        return new static(...$conditions);
    }

    public static function null(Condition $condition): static
    {
        if (!property_exists($condition, 'property')) {
            throw new ConditionHasNoProperty();
        }

        return new static($condition, new WhereIsNull($condition->property));
    }

    /** @var Condition[] */
    public array $conditions;

    /** @param Condition[] $conditions */
    public function __construct(...$conditions)
    {
        $this->conditions = $conditions;
    }
}
