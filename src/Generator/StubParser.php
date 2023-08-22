<?php

namespace Essa\APIToolKit\Generator;

use Essa\APIToolKit\Generator\DTOs\SchemaParserOutput;
use Illuminate\Support\Str;

class StubParser
{
    private array $patterns = [
        '/Dummy/',
        '/Dummies/',
        '/dummy/',
        '/dummies/',
        '/fillableColumns/',
        '/migrationContent/',
        '/resourceContent/',
        '/factoryContent/',
        '/createValidationRules/',
        '/updateValidationRules/',
        '/modelRelations/',
    ];

    private array $tags = [
        'soft-delete',
        'request',
        'resource',
        'filter',
    ];

    public function __construct(
        private string $model,
        private array $options,
        private SchemaParserOutput $schemaParserOutput)
    {
    }

    public function parseStub(string $type): string
    {
        $replacements = $this->getModelReplacements();

        $output = $this->replacePatternsInTheStub($replacements, $type);

        return $this->removeTags($output);
    }

    private function getModelReplacements(): array
    {
        return [
            $this->model,
            Str::plural($this->model),
            lcfirst($this->model),
            lcfirst(Str::plural($this->model)),
            $this->schemaParserOutput->fillableColumns,
            $this->schemaParserOutput->migrationContent,
            $this->schemaParserOutput->resourceContent,
            $this->schemaParserOutput->factoryContent,
            $this->schemaParserOutput->createValidationRules,
            $this->schemaParserOutput->updateValidationRules,
            $this->schemaParserOutput->modelRelations,
        ];
    }

    private function replacePatternsInTheStub(array $replacements, string $type): array|string|null
    {
        return preg_replace(
            $this->patterns,
            $replacements,
            $this->getStubContent($type)
        );
    }

    private function getStubContent(string $stubName): string
    {
        return file_get_contents(__DIR__ . "/../Stubs/{$stubName}.stub");
    }

    private function removeTags(string $string): string
    {
        $result = $string;

        foreach ($this->tags as $option) {
            $result = $this->removeTag(
                $result,
                $this->options[$option],
                $option
            );
        }

        return $result;
    }

    private function removeTag(string $string, $condition, string $tag): string
    {
        $pattern = $condition
            ? "/@if\(\'{$tag}\'\)|@endif\(\'{$tag}\'\)/"
            : "/@if\(\'{$tag}\'\)((?>[^@]++))*@endif\(\'{$tag}\'\)/";

        return preg_replace($pattern, '', $string);
    }
}
