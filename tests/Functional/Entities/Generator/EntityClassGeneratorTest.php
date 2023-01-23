<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional\Entities\Generator;

use Medas\EntityManager\Entities\Generator\EntityClassGenerator;
use Medas\EntityManagerTest\BaseTest;
use Medas\EntityManagerTest\MockUps\MockEntity;

class EntityClassGeneratorTest extends BaseTest
{
    const MOCK_ENTITY_EXPECTED_CLASS_CONTENT = <<<'PHP'
<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\MockUps;

use Medas\EntityManager\Attributes\{Entity, HasId, Id};
use Medas\EntityManager\Traits\Timestamps;
use Medas\EntityManager\Types\Guid;

#[Entity(store: 'mock_entities')]
class MockEntity implements HasId
{
    use Timestamps;

    #[Id, Guid]
    private string $guid;

    public function id(): string
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
