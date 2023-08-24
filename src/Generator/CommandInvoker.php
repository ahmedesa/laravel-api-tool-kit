<?php

namespace Essa\APIToolKit\Generator;

use Essa\APIToolKit\Generator\DTOs\GenerationConfiguration;
use Illuminate\Container\Container;

class CommandInvoker
{
    private array $defaultCommands = ['model'];

    public function __construct(private Container $container)
    {
    }

    public function executeCommands(GenerationConfiguration $generationConfiguration): void
    {
        foreach (config('api-tool-kit.api_generators.commands') as $option) {
            if ( ! $this->shouldExecute($option['option'], $generationConfiguration->getUserChoices())) {
                continue;
            }

            $this->container
                ->get($option['command'])
                ->run($generationConfiguration);
        }
    }

    private function shouldExecute(string $option, array $userChoices): bool
    {
        return in_array($option, $this->defaultCommands) || $userChoices[$option];
    }
}
