<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional\Entities\Generator;

use Medas\EntityManager\Entities\Generator\EntityClassGenerator;
use Medas\EntityManagerTest\{BaseTestClass, MockUps\MockEntity};

class EntityClassGeneratorTest extends BaseTestClass
{
    const MOCK_ENTITY_EXPECTED_CLASS_CONTENT
        = <<<'PHP'
<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\Core\Interfaces\{Guid, HasId};
use Medas\EntityManager\Attributes\{Entity, Id};
use Medas\EntityManager\Traits\Timestamps;

#[Entity(store: 'mock_entities')]
class MockEntity implements HasId
{
    use Timestamps;

    #[Id]
    public Guid $guid;

    public function id(): Guid
    {
        return $this->guid;
    }
}

PHP;

    public function testSimpleGeneration(): void
    {
        $content = service(EntityClassGenerator::class)->generate(MockEntity::class);
        $expected = self::MOCK_ENTITY_EXPECTED_CLASS_CONTENT;

        self::assertEquals($expected, $content);
    }

    public function testForwardSlashes(): void
    {
        $content = service(EntityClassGenerator::class)->generate(str_replace('\\', '/', MockEntity::class));
        $expected = self::MOCK_ENTITY_EXPECTED_CLASS_CONTENT;

        self::assertEquals($expected, $content);
    }

    public function testRootNamespace(): void
    {
        $content = service(EntityClassGenerator::class)->generate('./MockUps/MockEntity');
        $expected = self::MOCK_ENTITY_EXPECTED_CLASS_CONTENT;

        self::assertEquals($expected, $content);
    }
}
