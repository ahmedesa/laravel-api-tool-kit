<?php

namespace Essa\APIToolKit\Generator\Helpers;

use Essa\APIToolKit\Generator\Configs\PathConfigHandler;
use Essa\APIToolKit\Generator\GeneratedFileInfo;
use Illuminate\Support\Str;

class StubVariablesProvider
{
    public static function generate(string $modelName, string $pathGroup): array
    {
        $configForPathGroup = PathConfigHandler::generateFilePathsForAllTypes(
            pathGroupName: $pathGroup,
            modelName: $modelName
        );

        $replacements = [];

        foreach ($configForPathGroup as $type => $generatedFileInfo) {
            $replacements += self::generateReplacementsForType($type, $generatedFileInfo);
        }

        return $replacements + self::generateBasicReplacements($modelName);
    }

    private static function generateReplacementsForType(string $type, GeneratedFileInfo $generatedFileInfo): array
    {
        if ( ! $generatedFileInfo->getNamespace()) {
            return [];
        }

        $type = Str::studly($type);

        return [
            "{{Dummy{$type}}}" => $generatedFileInfo->getClassName(),
            "{{Dummy{$type}NameSpace}}" => $generatedFileInfo->getNameSpace(),
            "{{Dummy{$type}WithNameSpace}}" => $generatedFileInfo->getNameSpace() . '\\' . $generatedFileInfo->getClassName(),
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
