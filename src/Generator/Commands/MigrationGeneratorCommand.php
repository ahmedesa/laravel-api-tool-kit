<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\BaseGeneratorCommand;
use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\MigrationContentParser;
use Illuminate\Support\Str;

class MigrationGeneratorCommand extends BaseGeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        $schemaParser = new MigrationContentParser();
        $output = $schemaParser->parse($this->schema);

        return [
            'migrationContent' => $output,
        ];
    }
    protected function getStubName(): string
    {
        return 'dummy_migration';
    }

    protected function getOutputFolder(): string
    {
        return database_path('migrations');
    }

    protected function getOutputFilePath(): string
    {
        $migrationFileName = $this->getMigrationTableName();
        return database_path("migrations/{$migrationFileName}");
    }

    private function getMigrationTableName(): string
    {
        $migrationClass = 'create_' . Str::plural(Str::snake($this->model)) . '_table';

        return date('Y_m_d_His') . "_{$migrationClass}.php";
    }
}
