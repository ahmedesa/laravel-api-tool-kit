<?php

namespace Essa\APIToolKit\Generator\Handlers;

use Essa\APIToolKit\Commands\GeneratorCommand;

class UserChoicesHandler
{
    private GeneratorCommand $command;

    private array $allOptions = [
        'controller',
        'request',
        'resource',
        'migration',
        'factory',
        'seeder',
        'filter',
        'test',
        'routes',
    ];

    public function handel(): array
    {
        $allDefaultSelected = $this->command->option('all');

        $userChoices = $allDefaultSelected
            ? $this->setDefaultOptions()
            : $this->gatherUserOptions();

        return $userChoices + ['soft-delete' => $this->command->option('soft-delete')];
    }

    public function setCommand(GeneratorCommand $command): self
    {
        $this->command = $command;

        return $this;
    }

    private function setDefaultOptions(): array
    {
        $userChoices = [];

        foreach (config('api-tool-kit.default_generates') as $option) {
            if (in_array($option, $this->allOptions)) {
                $userChoices[$option] = true;
            }
        }

        return $userChoices;
    }

    private function gatherUserOptions(): array
    {
        return $this->command->options();
    }
}
