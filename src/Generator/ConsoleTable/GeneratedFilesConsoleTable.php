<?php

namespace Essa\APIToolKit\Generator\ConsoleTable;

use Essa\APIToolKit\Generator\ApiGenerationCommandInputs;
use Essa\APIToolKit\Generator\Configs\PathConfigHandler;
use Essa\APIToolKit\Generator\Contracts\ConsoleTableInterface;
use Essa\APIToolKit\Generator\TableDate;

class GeneratedFilesConsoleTable implements ConsoleTableInterface
{
    public function generate(ApiGenerationCommandInputs $apiGenerationCommandInputs): TableDate
    {
        $tableData = $this->generateTableData($apiGenerationCommandInputs);

        $headers = ['Type', 'File Path'];

        return new TableDate($headers, $tableData);
    }

    private function generateTableData(ApiGenerationCommandInputs $apiGenerationCommandInputs): array
    {
        $configForPathGroup = PathConfigHandler::getConfigForPathGroup($apiGenerationCommandInputs->getPathGroup());

        $tableData = [];

        foreach ($configForPathGroup as $type => $pathResolver) {
            if ($apiGenerationCommandInputs->isOptionSelected($type)) {
                $tableData[] = [
                    $type,
                    (new $pathResolver($apiGenerationCommandInputs->getModel()))->getFullPath(),
                ];
            }
        }

        return $tableData;
    }
}
