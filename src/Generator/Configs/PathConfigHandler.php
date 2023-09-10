<?php

namespace Essa\APIToolKit\Generator\Configs;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
use Essa\APIToolKit\Generator\Exception\ConfigNotFoundException;
use Illuminate\Support\Facades\Config;

class PathConfigHandler
{
    /**
     * Get the configuration array associated with a specific path group.
     *
     * @param string $pathGroup The name of the path group.
     *
     * @return array The configuration array for the specified path group.
     */
    public static function getConfigForPathGroup(string $pathGroup): array
    {
        return Config::get("api-tool-kit.generator_path_groups.{$pathGroup}", []);
    }

    /**
     * Get the class name (as a string) of a path resolver associated with a specific path group and type.
     *
     * @param string $pathGroup The name of the path group.
     * @param string $type      The type of the path resolver.
     *
     * @return string|null The class name of the path resolver or null if not found.
     */
    public static function getPathResolverClass(string $pathGroup, string $type): ?string
    {
        $config = self::getConfigForPathGroup($pathGroup);

        return $config[$type] ?? null;
    }

    /**
     * Create an instance of a path resolver class based on the specified path group, type, and model.
     *
     * @param string $pathGroup      The name of the path group.
     * @param string $type           The type of the path resolver.
     * @param string $model          The model associated with the path resolver.
     *
     * @return PathResolverInterface An instance of the path resolver implementing PathResolverInterface.
     *
     * @throws ConfigNotFoundException If the path resolver class is not found for the specified type.
     */
    public static function createPathResolverInstance(string $pathGroup, string $type, string $model): PathResolverInterface
    {
        $pathResolverClass = self::getPathResolverClass($pathGroup, $type);

        if ( ! $pathResolverClass) {
            throw new ConfigNotFoundException("Path resolver class not found for type: {$type}");
        }

        return new $pathResolverClass($model);
    }

    /**
     * Process different path types within a path group using a callback function.
     *
     * @param string   $pathGroup The name of the path group.
     * @param callable $callback  The callback function to process each path type.
     *
     * @return array An array of results produced by the callback for each path type.
     */
    public static function iterateOverTypesPathsFromConfig(string $pathGroup, callable $callback): array
    {
        $configForPathGroup = self::getConfigForPathGroup($pathGroup);

        $results = [];

        foreach ($configForPathGroup as $type => $pathResolver) {
            $results = $results + call_user_func($callback, $type, $pathResolver);
        }

        return $results;
    }

    /**
     * Get the default path group from the configuration.
     *
     * @return string The name of the default path group.
     */
    public static function getDefaultPathGroup(): string
    {
        return Config::get('api-tool-kit.default_path_groups', 'default');
    }

    /**
     * Get all path groups names from configuration.
     *
     * @return array The name of all path groups.
     */
    public static function getAllPathGroups(): array
    {
        return array_keys(Config::get('api-tool-kit.generator_path_groups'));
    }

    /**
     * check if path group exist or not
     *
     * @param string $groupName
     * @return bool
     */
    public static function isValidPathGroup(string $groupName): bool
    {
        return (bool) Config::get("api-tool-kit.generator_path_groups.{$groupName}");
    }
}
