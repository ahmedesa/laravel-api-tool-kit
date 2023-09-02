<?php

namespace Essa\APIToolKit\Generator;

class TableDate
{
    public function __construct(private array $headers, private array $tableData)
    {
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getTableData(): array
    {
        return $this->tableData;
    }
}
