<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator;

use Medas\EntityManager\Entities\Generator\{Exceptions\ClassHasNoNamespace, NameConverters\NameConverter};
use Medas\ServiceManager\Attributes\Service;

#[Service]
class EntityClassGenerator
{
    private const PHP_TEMPLATE = <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\EntityManager\Attributes\{Entity, HasId, Id};
use Medas\EntityManager\Traits\Timestamps;
use Medas\ServiceManager\Values\Interfaces\Guid;

#[Entity(store: '{{storeName}}')]
class {{shortClassName}} implements HasId
{
    use Timestamps;

    #[Id]
    private Guid $guid;

    public function id(): Guid
    {
        return $this->guid;
    }
}

PHP;

    public function __construct(
        private readonly ClassNameNormalizer $classNameNormalizer,
        private readonly FileNameFinder      $fileNameFinder,
        private readonly NameConverter       $storeNameConverter,
    )
    {
    }

    public function generate(string $className): string
    {
        $className = $this->classNameNormalizer->normalize($className);

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
}
