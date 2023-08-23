<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class SeederGeneratorCommand extends BaseGeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummySeeder';
    }

    protected function getOutputFolder(): string
    {
        return database_path('/seeders');
    }

    protected function getOutputFilePath(): string
    {
        return database_path("seeders/{$this->model}Seeder.php");
    }
}
