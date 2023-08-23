<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Essa\APIToolKit\Generator\SchemaParsers\MigrationContentParser;
use Illuminate\Support\Str;

class GeneratorMigrationCommand extends BaseGeneratorCommand
{
    protected function getStub(): string
    {
        return 'dummy_migration'; // Replace with the name of your migration stub
    }

    protected function getFolder(): string
    {
        return database_path('migrations');
    }

    protected function getFullPath(): string
    {
        $migrationFileName = $this->getMigrationTableName();
        return database_path("migrations/{$migrationFileName}");
    }

    protected function schemaReplacements(): array
    {
        $schemaParser = new MigrationContentParser();
        $output = $schemaParser->parse($this->schema);

        return [
            'migrationContent' => $output,
        ];
    }

    private function getMigrationTableName(): string
    {
        $migrationClass = 'create_' . Str::plural(Str::snake($this->model)) . '_table';

        return date('Y_m_d_His') . "_{$migrationClass}.php";
    }
}
