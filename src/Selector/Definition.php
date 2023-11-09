<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector;

class Definition
{
    public static function create(string $entity): static
    {
        return new static($entity);
    }

    /** @var Relations\Relation[] */
    public array $relations = [];

    /** @var Conditions\Condition[] */
    public array $conditions = [];

    /** @var Sorting\SortBy[] */
    public array $sorts = [];

    /** @var Parameter[] */
    public array $parameters = [];
    public Pagination|null $pagination = null;

    public function __construct(
        public string $entity,
    )
    {
    }

    public function add(Element $element): self
    {
        match (true) {
            $element instanceof Relations\Relation => $this->relations[] = $element,
            $element instanceof Conditions\Condition => $this->conditions[] = $element,
            $element instanceof Sorting\SortBy => $this->sorts[] = $element,
            $element instanceof Parameter => $this->parameters[] = $element,
            $element instanceof Pagination => $this->pagination = $element,

            default => throw new \Exception('unhandled element type ' . $element::class),
        };

        return $this;
    }
}
