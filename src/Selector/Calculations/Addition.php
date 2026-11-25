<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Calculations;

/**
 * The calculations added up, such as the places a contact is used, counted per kind of
 * holder. A value of the row itself: OutputValues\Sum adds up a column over the rows of
 * a group instead.
 */
class Addition implements Calculation
{
    public static function c(Calculation ...$terms): static
    {
        return new static(...$terms);
    }

    /** @var Calculation[] */
    public array $terms;

    public function __construct(Calculation ...$terms)
    {
        $this->terms = $terms;
    }
}
