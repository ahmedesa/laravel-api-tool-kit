<?php

namespace Essa\APIToolKit\Generator;

use Essa\APIToolKit\Generator\Contracts\PathHasClass;
use Essa\APIToolKit\Generator\PathResolver\PathResolver;
use Illuminate\Support\Str;

class PlaceholderReplacements
{
    public static function generate(string $modelName): array
    {
        $replacements = [];

        $config = config('api-tool-kit-internal.api_generators.options');

        foreach ($config as $type => $options) {
            $replacements += self::generateReplacementsForType($type, $options['path_resolver'], $modelName);
        }

        return $replacements + self::generateBasicReplacements($modelName);
    }

    private static function generateReplacementsForType(string $type, string $pathResolverClass, string $modelName): array
    {
        /** @var PathResolver $resolver */
        $resolver = new $pathResolverClass($modelName);

        if ( ! $resolver instanceof PathHasClass) {
            return [];
        }

        $type = Str::studly($type);

        return [
            "{{Dummy{$type}}}" => $resolver->getClassName(),
            "{{Dummy{$type}NameSpace}}" => $resolver->getNameSpace(),
            "{{Dummy{$type}WithNameSpace}}" => $resolver->getNameSpace() . '\\' . $resolver->getClassName(),
        ];
    }

    private static function generateBasicReplacements(string $modelName): array
    {
        return [
            '{{Dummy}}' => $modelName,
            '{{Dummies}}' => Str::plural($modelName),
            '{{dummy}}' => lcfirst($modelName),
            '{{dummies}}' => lcfirst(Str::plural($modelName)),
        ];
    }
}
