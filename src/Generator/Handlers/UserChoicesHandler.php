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
        $yesOrNo = [
            'y' => 'Yes',
            'n' => 'No',
        ];

        $allDefaultSelected = $this->command->choice(
            'Select all default options ?',
            $yesOrNo,
            'y'
        );

        $useSoftDelete = $this->command->choice(
            'Do you want to use <options=bold>soft delete</> ?',
            $yesOrNo,
            'y'
        );

        $userChoices = 'y' === $allDefaultSelected
            ? $this->setDefaultOptions()
            : $this->gatherUserOptions();

        return $userChoices + ['soft-delete' => 'y' === $useSoftDelete];
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
        $userChoices = [];
        foreach ($this->allOptions as $option) {
            $choice = $this->command->choice(
                "Do you want to generate <options=bold>{$option}</> ?",
                ['y' => 'Yes', 'n' => 'No'],
                'y'
            );
            $userChoices[$option] = 'y' === $choice;
        }

        return $userChoices;
    }
}
