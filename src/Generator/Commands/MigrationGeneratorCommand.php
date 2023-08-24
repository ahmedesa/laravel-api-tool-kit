<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\PathResolver\MigrationPathResolver;
use Essa\APIToolKit\Generator\SchemaParsers\MigrationContentParser;

class MigrationGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    public function getSchemaReplacements(): array
    {
        return [
            'migrationContent' => (new MigrationContentParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'dummy_migration';
    }

    protected function getOutputFilePath(): PathResolverInterface
    {
        return new MigrationPathResolver($this->apiGenerationCommandInputs->getModel());
    }
}
