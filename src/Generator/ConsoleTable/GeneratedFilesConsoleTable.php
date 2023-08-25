<?php

namespace Essa\APIToolKit\Generator\ConsoleTable;

use Essa\APIToolKit\Generator\Contracts\ConsoleTableInterface;
use Essa\APIToolKit\Generator\DTOs\ApiGenerationCommandInputs;
use Essa\APIToolKit\Generator\DTOs\TableDate;

class GeneratedFilesConsoleTable implements ConsoleTableInterface
{
    public function generate(ApiGenerationCommandInputs $apiGenerationCommandInputs): TableDate
    {
        $apiGeneratorOptions = config('api-tool-kit.api_generators.options');

        $tableData = [];

        foreach ($apiGeneratorOptions as $option => $config) {
            if ($apiGenerationCommandInputs->isOptionSelected($option)) {
                $resolverFilePath = $config['path_resolver'];
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
