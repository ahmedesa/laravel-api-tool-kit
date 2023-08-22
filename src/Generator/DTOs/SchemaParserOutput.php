<?php

namespace Essa\APIToolKit\Generator\DTOs;

class SchemaParserOutput
{
    public function __construct(
        public string  $fillableColumns = '',
        public string $migrationContent = '',
        public string $resourceContent = '',
        public string $factoryContent = '',
        public string $createValidationRules = '',
        public string $updateValidationRules = ''
    ) {
    }
}
