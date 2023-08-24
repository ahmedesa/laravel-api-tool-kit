<?php

namespace Essa\APIToolKit\Generator\Commands;

class SeederGeneratorCommand extends GeneratorCommand
{
    protected function getStubName(): string
    {
        return 'DummySeeder';
    }

    protected function getOutputFolderPath(): string
    {
        return database_path('/seeders');
    }

    protected function getOutputFileName(): string
    {
        return "{$this->generationConfiguration->getModel()}Seeder.php";
    }
}
