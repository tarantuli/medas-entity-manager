<?php

declare(strict_types=1);

namespace Medas\EntityManagerTest\Functional\Entities\Generator;

use Medas\EntityManager\Entities\Generator\EntityClassGenerator;
use Medas\EntityManagerTest\BaseTest;
use Medas\EntityManagerTest\MockUps\MockEntity;

class EntityClassGeneratorTest extends BaseTest
{
    public function testSimpleGeneration(): void
    {
        $content = service(EntityClassGenerator::class)->generate(MockEntity::class);

        $expected = <<<'PHP'
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
        self::assertEquals($expected, $content);
    }
}
