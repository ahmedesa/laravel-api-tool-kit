<?php

namespace Essa\APIToolKit\Generator\ConsoleTable;

use Essa\APIToolKit\Generator\ApiGenerationCommandInputs;
use Essa\APIToolKit\Generator\Contracts\ConsoleTableInterface;
use Essa\APIToolKit\Generator\TableDate;

class SchemaConsoleTable implements ConsoleTableInterface
{
    public function generate(ApiGenerationCommandInputs $apiGenerationCommandInputs): TableDate
    {
        $tableData = [];

        foreach ($apiGenerationCommandInputs->getSchema()->getColumns() as $column) {
            $tableData[] = [$column->getName(), $column->getType(), $column->getOptionsAsString()];
        }

        $headers = ['Column Name', 'Column Type', 'Options'];

        return new TableDate($headers, $tableData);
    }
}
