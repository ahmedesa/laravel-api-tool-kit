<?php

namespace Essa\APIToolKit\Generator\Commands;

class SeederGeneratorCommand extends GeneratorCommand
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
        return database_path("seeders/{$this->generationConfiguration->getModel()}Seeder.php");
    }
}
