<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\MigrationContentParser;

class MigrationGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    protected string $type = 'migration';

    public function getSchemaReplacements(): array
    {
        return [
            'migrationContent' => (new MigrationContentParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'dummyMigration';
    }
}
