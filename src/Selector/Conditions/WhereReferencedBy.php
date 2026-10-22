<?php

declare(strict_types=1);

namespace Medas\EntityManager\Selector\Conditions;

/**
 * True when at least one row of another entity refers to this row and meets the
 * given conditions. For "a supplier subscription property whose supplier
 * subscription is referred to by a subscription of one of these tenants":
 *
 *     WhereReferencedBy::c(
 *         Subscription::class,
 *         'supplierSubscription',
 *         'supplierSubscription',
 *         WhereIn::c(Property::c('tenant', Subscription::class), Values::c($tenants)),
 *     )
 *
 * The referring entity's property is compared with this entity's own property
 * (name the id property to mean the row itself). A row referred to several times
 * is still selected once, which a join along the back-reference would not do.
 *
 * In the conditions, a property of the referring entity names that entity
 * explicitly; a property without an entity is one of the selected entity, as
 * anywhere else. The referring entity cannot be the selected entity itself.
 */
class WhereReferencedBy implements Condition
{
    public static function c(
        string    $referringEntity,
        string    $referringProperty,
        string    $property,
        Condition ...$conditions
    ): static
    {
        return new static($referringEntity, $referringProperty, $property, ...$conditions);
    }

    /** @var Condition[] */
    public array $conditions;

    public function __construct(
        public string $referringEntity,
        public string $referringProperty,
        public string $property,
        Condition     ...$conditions
    )
    {
        $this->conditions = $conditions;
    }
}
