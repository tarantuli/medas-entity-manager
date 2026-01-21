<?php

declare(strict_types=1);

namespace Medas\EntityManager\Entities\Generator;

use Medas\Core\Attributes\Service;

#[Service]
readonly class CollectionClassGenerator
{
    private const string PHP_TEMPLATE
        = <<<'PHP'
<?php

declare(strict_types=1);

namespace {{namespace}};

use Medas\EntityManager\Attributes\EntityCollection;
use Medas\StorageManager\Entities\RecordCollection;

/**
 * @extends RecordCollection<{{shortClassName}}>
 */
#[EntityCollection({{shortClassName}}::class)]
class {{shortClassName}}Collection extends RecordCollection
{
}

PHP;

    public function __construct(
        private ClassNameNormalizer $classNameNormalizer,
    )
    {
    }

    public function generate(string $className): string
    {
        $className = $this->classNameNormalizer->normalize($className);
        [$namespace, $shortClassName] = $this->splitClassName($className);

        $replacements = [
            '{{namespace}}' => $namespace,
            '{{shortClassName}}' => $shortClassName,
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
            throw new Exceptions\ClassHasNoNamespace($className);
        }

        return [
            substr($className, 0, $pos),
            substr($className, $pos + 1),
        ];
    }
}
