<?php

namespace Essa\APIToolKit\Generator\Configs;

use Essa\APIToolKit\Generator\Exception\ConfigNotFoundException;
use Illuminate\Support\Facades\Config;

class CommandConfigHandler
{
    /**
     * Get all API generator commands from the configuration.
     *
     * @return array An array of API generator commands.
     */
    public static function getAllCommands(): array
    {
        return Config::get('api-tool-kit-internal.api_generator_commands', []);
    }

    /**
     * Get the class name (as a string) of an API generator command.
     *
     * @param string $commandName The name of the API generator command.
     *
     * @return string|null The class name of the API generator command or null if not found.
     * @throws ConfigNotFoundException If the configuration for the command is not found.
     */
    public static function getCommandClass(string $commandName): ?string
    {
        $commandClass = Config::get("api-tool-kit.api_generator_commands.{$commandName}");

        if ( ! $commandClass) {
            throw new ConfigNotFoundException("API generator command configuration not found for command: {$commandName}");
        }

        return $commandClass;
    }

    /**
     * Process different API generator commands using a callback function.
     *
     * @param callable $callback The callback function to process each API generator command.
     * @throws ConfigNotFoundException If the configuration for a command is not found.
     */
    public static function iterateOverCommandsFromConfig(callable $callback): void
    {
        $apiGeneratorCommands = Config::get('api-tool-kit.api_generator_commands', []);

        foreach ($apiGeneratorCommands as $type => $commandClass) {
            call_user_func($callback, $type, $commandClass);
        }
    }
}
