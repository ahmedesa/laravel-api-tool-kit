<?php

namespace Essa\APIToolKit\Generator\SchemaParsers;

use Essa\APIToolKit\Generator\DTOs\ColumnDefinition;
use Essa\APIToolKit\Generator\DTOs\SchemaDefinition;
use Essa\APIToolKit\Generator\Guessers\FactoryMethodGuesser;

class FactoryColumnsParser extends SchemaParser
{
    protected function getParsedSchema(SchemaDefinition $schemaDefinition): string
    {
        return collect($schemaDefinition->getColumns())
            ->map(fn (ColumnDefinition $definition): string => $this->generateFactoryColumnDefinition($definition))
            ->implode(PHP_EOL . "\t\t\t");
    }

    private function generateFactoryColumnDefinition(ColumnDefinition $definition): string
    {
        $factoryMethodGuesser = new FactoryMethodGuesser($definition);
        $factoryMethod = $factoryMethodGuesser->guess();

        return "'{$definition->getName()}' => \$this->faker->{$factoryMethod}(),";
    }
}
