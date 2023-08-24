<?php

namespace Essa\APIToolKit\Generator\ConsoleTable;

use Essa\APIToolKit\Generator\Contracts\ConsoleTableInterface;
use Essa\APIToolKit\Generator\DTOs\ApiGenerationCommandInputs;
use Essa\APIToolKit\Generator\DTOs\TableDate;

class GeneratedFilesConsoleTable implements ConsoleTableInterface
{
    public function __construct(private ApiGenerationCommandInputs $apiGenerationCommandInputs)
    {
    }

    public function generate(): TableDate
    {
        $commandDefinitions = config('api-tool-kit.api_generators.commands');

        $tableData = [];

        foreach ($commandDefinitions as $definition) {
            if ($this->shouldExecute($definition['option'])) {
                $resolverFilePath = $definition['path-resolver'];
                $tableData[] = [
                    $definition['option'],
                    (new $resolverFilePath($this->apiGenerationCommandInputs->getModel()))->getFullPath()
                ];
            }
        }

        $headers = ['Option', 'File Path'];

        return new TableDate($headers, $tableData);
    }

    private function shouldExecute(string $option): bool
    {
        return 'model' === $option || $this->apiGenerationCommandInputs->getUserChoices()[$option];
    }
}
