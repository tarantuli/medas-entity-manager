<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional;

use Medas\Core\Date;
use Medas\EntityManager\Exceptions\InvalidPropertyType;
use Medas\EntityManagerTest\BaseTestClass;
use Medas\EntityManagerTest\MockUps\{
    Casting\DatedEntity,
    Inheritance\ConcreteRule,
    Inheritance\RuleTier,
    Inheritance\UnrelatedEntity
};

class ValueCasterTest extends BaseTestClass
{
    // A relation typed as an abstract parent takes an entity of a concrete subclass as
    // it is. It used to be taken as the id of a reference, which failed to make a key of
    // the object.
    public function testRelationTypedAsAParentTakesASubclassEntity(): void
    {
        $rule = new ConcreteRule();

        $rule->id = 1;

        /** @var RuleTier $tier */
        $tier = $this->entityManager()->create(RuleTier::class, ['rule' => $rule]);

        self::assertSame($rule, $tier->rule);
    }

    public function testRelationRefusesAnEntityOfAnUnrelatedClass(): void
    {
        $unrelated = new UnrelatedEntity();

        $unrelated->id = 1;

        $this->expectException(InvalidPropertyType::class);
        $this->entityManager()->create(RuleTier::class, ['rule' => $unrelated]);
    }

    // A date object is converted rather than parsed as a string, which it isn't
    public function testDatePropertiesTakeDateObjects(): void
    {
        $moment = new \DateTimeImmutable('2026-11-26 13:20:00');

        /** @var DatedEntity $entity */
        $entity = $this->entityManager()->create(
            DatedEntity::class,
            ['day' => $moment, 'moment' => $moment]
        );

        self::assertEquals(new Date(2026, 11, 26), $entity->day);
        self::assertInstanceOf(\DateTime::class, $entity->moment);
        self::assertSame('2026-11-26 13:20:00', $entity->moment->format('Y-m-d H:i:s'));
    }
}
