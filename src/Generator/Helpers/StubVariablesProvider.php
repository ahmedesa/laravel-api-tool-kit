<?php

namespace Essa\APIToolKit\Generator\Helpers;

use Essa\APIToolKit\Generator\Configs\PathConfigHandler;
use Essa\APIToolKit\Generator\Contracts\HasClassAndNamespace;
use Essa\APIToolKit\Generator\PathResolver\PathResolver;
use Illuminate\Support\Str;

class StubVariablesProvider
{
    public static function generate(string $modelName, string $pathGroup): array
    {
        $configForPathGroup = PathConfigHandler::getConfigForPathGroup($pathGroup);

        $replacements = [];

        foreach ($configForPathGroup as $type => $pathResolver) {
            $replacements += self::generateReplacementsForType($type, $pathResolver, $modelName);
        }

        return $replacements + self::generateBasicReplacements($modelName);
    }

    private static function generateReplacementsForType(string $type, string $pathResolverClass, string $modelName): array
    {
        /** @var PathResolver $resolver */
        $resolver = new $pathResolverClass($modelName);

        if ( ! $resolver instanceof HasClassAndNamespace) {
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
