<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator;

use Medas\Core\Attributes\Service;

#[Service]
readonly class EntityClassGenerator
{
    private const PHP_GUID_TEMPLATE
        = <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\Interfaces\{Guid, HasId};
use Medas\EntityManager\Attributes\{Entity, Id};
use Medas\EntityManager\Traits\Timestamps;

#[Entity(store: '{{storeName}}')]
class {{shortClassName}} implements HasId
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

    private const PHP_INT_TEMPLATE
        = <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\Interfaces\HasId;
use Medas\EntityManager\Attributes\{Entity, Id};
use Medas\EntityManager\Traits\Timestamps;

#[Entity(store: '{{storeName}}')]
class {{shortClassName}} implements HasId
{
    use Timestamps;

    #[Id]
    public int $id;

    public function id(): int
    {
        return $this->id;
    }
}

PHP;

    public function __construct(
        private ClassNameNormalizer          $classNameNormalizer,
        private FileNameFinder               $fileNameFinder,
        private NameConverters\NameConverter $storeNameConverter,
    )
    {
    }

    public function generate(string $className, bool $useGuid = true): string
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
            $useGuid ? self::PHP_GUID_TEMPLATE : self::PHP_INT_TEMPLATE
        );
    }

    private function splitClassName(string $className): array
    {
        $pos = strrpos($className, '\\');

        if ($pos === false) {
            throw new Exceptions\ClassHasNoNamespace($className);
        }

        return [
            substr($className, 0, $pos),
            substr($className, $pos + 1),
        ];
    }
}
