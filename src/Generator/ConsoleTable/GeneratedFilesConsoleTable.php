<?php

namespace Essa\APIToolKit\Generator\ConsoleTable;

use Essa\APIToolKit\Generator\Contracts\ConsoleTableInterface;
use Essa\APIToolKit\Generator\DTOs\ApiGenerationCommandInputs;
use Essa\APIToolKit\Generator\DTOs\TableDate;

class GeneratedFilesConsoleTable implements ConsoleTableInterface
{
    public function generate(ApiGenerationCommandInputs $apiGenerationCommandInputs): TableDate
    {
        $commandDefinitions = config('api-tool-kit.api_generators.commands');

        $tableData = [];

        foreach ($commandDefinitions as $option => $definition) {
            if ($apiGenerationCommandInputs->isOptionSelected($option)) {
                $resolverFilePath = $definition['path-resolver'];
                $tableData[] = [
                    $option,
                    (new $resolverFilePath($apiGenerationCommandInputs->getModel()))->getFullPath()
                ];
            }
        }

        $headers = ['Type', 'File Path'];

        return new TableDate($headers, $tableData);
    }
}
