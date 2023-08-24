<?php

namespace Essa\APIToolKit\Generator\ConsoleTable;

use Essa\APIToolKit\Generator\Contracts\ConsoleTableInterface;
use Essa\APIToolKit\Generator\DTOs\SchemaDefinition;
use Essa\APIToolKit\Generator\DTOs\TableDate;

class SchemaConsoleTable implements ConsoleTableInterface
{
    public function __construct(private SchemaDefinition $schemaDefinition)
    {
    }

    public function generate(): TableDate
    {
        $tableData = [];

        foreach ($this->schemaDefinition->getColumns() as $column) {
            $tableData[] = [$column->getName(), $column->getType(), $column->getOptionsAsString()];
        }

        $headers = ['Column Name', 'Column Type', 'Options'];

        return new TableDate($headers, $tableData);
    }
}
