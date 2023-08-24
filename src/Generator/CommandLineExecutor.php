<?php

namespace Essa\APIToolKit\Generator;

use Essa\APIToolKit\Generator\Commands\ControllerGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\CreateFormRequestGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\FactoryGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\FilterGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\MigrationGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\ModelGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\ResourceGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\RoutesGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\SeederGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\TestGeneratorCommand;
use Essa\APIToolKit\Generator\Commands\UpdateFormRequestGeneratorCommand;
use Essa\APIToolKit\Generator\DTOs\GenerationConfiguration;

class CommandLineExecutor
{
    private array $commands = [
        'model' => ModelGeneratorCommand::class,
        'factory' => FactoryGeneratorCommand::class,
        'seeder' => SeederGeneratorCommand::class,
        'controller' => ControllerGeneratorCommand::class,
        'test' => TestGeneratorCommand::class,
        'resource' => ResourceGeneratorCommand::class,
        'request' => [
            CreateFormRequestGeneratorCommand::class,
            UpdateFormRequestGeneratorCommand::class,
        ],
        'filter' => FilterGeneratorCommand::class,
        'migration' => MigrationGeneratorCommand::class,
        'routes' => RoutesGeneratorCommand::class,
    ];

    public function executeCommands(GenerationConfiguration $generationConfiguration): void
    {
        foreach ($this->commands as $option => $commandClasses) {
            if ( ! $this->shouldExecute($option, $generationConfiguration->getUserChoices())) {
                continue;
            }

            foreach ((array)$commandClasses as $commandClass) {
                app($commandClass)->run($generationConfiguration);
            }
        }
    }

    private function shouldExecute(string $option, $userChoices): bool
    {
        return 'model' === $option || $userChoices[$option];
    }
}
