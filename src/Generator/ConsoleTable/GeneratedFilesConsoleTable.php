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

        return new TableDate($headers, [$tableData]);
    }

    private function generateTableData(ApiGenerationCommandInputs $apiGenerationCommandInputs): array
    {
        return PathConfigHandler::iterateOverTypesPathsFromConfig(
            pathGroup: $apiGenerationCommandInputs->getPathGroup(),
            callback: fn (string $type, string $pathResolver) => $this->generateTableRow($type, $pathResolver, $apiGenerationCommandInputs)
        );
    }

    private function generateTableRow(string $type, string $pathResolver, ApiGenerationCommandInputs $apiGenerationCommandInputs): array
    {
        if ( ! $apiGenerationCommandInputs->isOptionSelected($type)) {
            return [];
        }

        return [
            $type,
            (new $pathResolver($apiGenerationCommandInputs->getModel()))->getFullPath(),
        ];
    }
}
