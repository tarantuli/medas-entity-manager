<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator;

use Medas\EntityManager\ConfigOptions\GeneratorRootNamespace;
use Medas\EntityManager\Entities\Generator\{Exceptions\ClassHasNoNamespace, NameConverters\NameConverter};
use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\ConfigOptions\ConfigValue;

#[Service]
class EntityClassGenerator
{
    private const PHP_TEMPLATE = <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\EntityManager\Attributes\{Entity, HasId, Id};
use Medas\EntityManager\Traits\Timestamps;
use Medas\EntityManager\Types\Guid;

#[Entity(store: '{{storeName}}')]
class {{shortClassName}} implements HasId
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

    public function __construct(
        #[ConfigValue(GeneratorRootNamespace::class)]
        private readonly string|null    $rootNamespace,
        private readonly FileNameFinder $fileNameFinder,
        private readonly NameConverter  $storeNameConverter,
    )
    {
    }

    public function generate(string $className): string
    {
        $className = $this->normalizeClassName($className);

        [$namespace, $shortClassName] = $this->splitClassName($className);
        $storeName = $this->storeNameConverter->convert($shortClassName);

        $replacements = [
            '{{namespace}}' => $namespace,
            '{{shortClassName}}' => $shortClassName,
            '{{storeName}}' => $storeName,
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            self::PHP_TEMPLATE
        );
    }

    private function splitClassName(string $className): array
    {
        $pos = strrpos($className, '\\');

        if ($pos === false) {
            throw new ClassHasNoNamespace($className);
        }

        return [
            substr($className, 0, $pos),
            substr($className, $pos + 1),
        ];
    }

    private function normalizeClassName(string $className): string|array
    {
        // Replace a leading dot by the root namespace
        if (str_starts_with($className, '.')) {
            $className = $this->rootNamespace . substr($className, 1);
        }

        // Replace forward slashes by backward slashes
        return str_replace('/', '\\', $className);
    }
}
