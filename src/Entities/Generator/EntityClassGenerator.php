<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator;

use Medas\Core\Attributes\Service;

#[Service]
readonly class EntityClassGenerator
{
    private const PHP_UUID_TEMPLATE
        = <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\Core\Interfaces\{Uuid, HasId};
use Medas\EntityManager\Attributes\{Entity, Id};
use Medas\EntityManager\Traits\Timestamps;

#[Entity(store: '{{storeName}}')]
class {{shortClassName}} implements HasId
{
    use Timestamps;

    #[Id]
    public Uuid $id;

    public function id(): Uuid
    {
        return $this->id;
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
        private NameConverters\NameConverter $storeNameConverter,
    )
    {
    }

    public function generate(string $className, bool $useUuid = true): string
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
            $useUuid ? self::PHP_UUID_TEMPLATE : self::PHP_INT_TEMPLATE
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
