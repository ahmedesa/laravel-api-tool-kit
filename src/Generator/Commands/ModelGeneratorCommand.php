<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\SchemaParsers\FillableColumnsParser;
use Essa\APIToolKit\Generator\SchemaParsers\RelationshipMethodsParser;

class ModelGeneratorCommand extends GeneratorCommand implements SchemaReplacementDataProvider
{
    protected string $type = 'model';

    public function getSchemaReplacements(): array
    {
        return [
            'fillableColumns' =>  (new FillableColumnsParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
            'modelRelations' => (new RelationshipMethodsParser($this->apiGenerationCommandInputs->getSchema()))->parse(),
        ];
    }
    protected function getStubName(): string
    {
        return 'Dummy';
    }
}
