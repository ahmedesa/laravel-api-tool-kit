<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\MigrationContentParser;
use Illuminate\Support\Str;

class MigrationGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'migrationContent' => (new MigrationContentParser($this->generationConfiguration->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'dummy_migration';
    }

    protected function getOutputFolderPath(): string
    {
        return database_path('migrations');
    }

    protected function getOutputFileName(): string
    {
        return $this->getMigrationTableName();
    }

    private function getMigrationTableName(): string
    {
        $migrationClass = 'create_' . Str::plural(Str::snake($this->generationConfiguration->getModel())) . '_table';

        return date('Y_m_d_His') . "_{$migrationClass}.php";
    }
}
