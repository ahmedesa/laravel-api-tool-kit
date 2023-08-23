<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;

class GeneratorSeederCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'DummySeeder'; // Replace with the name of your seeder stub
    }

    protected function getFolder(): string
    {
        return database_path('/seeders');
    }

    protected function getFullPath(): string
    {
        return database_path("seeders/{$this->model}Seeder.php");
    }
}
