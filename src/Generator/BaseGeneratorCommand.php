<?php

namespace Essa\APIToolKit\Generator;

use Essa\APIToolKit\Generator\DTOs\SchemaParserOutput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

abstract class BaseGeneratorCommand
{
    protected const PATTERNS = [
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

    protected const TAGS = [
        'soft-delete',
        'request',
        'resource',
        'filter',
    ];

    public function __construct(
        protected string             $model,
        protected array              $options,
        protected SchemaParserOutput $schemaParserOutput,
        protected ?string            $schema = null
    ) {
    }

    public function handle(): void
    {
        if ( ! file_exists($this->getFolder())) {
            $this->createDirectory();
        }

        $this->saveContentInTheFilePath();
    }

    public function parseStub(string $type): string
    {
        $replacements = $this->getModelReplacements();

        $output = $this->replacePatternsInTheStub($replacements, $type);

        return $this->removeTags($output);
    }

    abstract protected function getStub(): string;

    abstract protected function getFolder(): string;

    abstract protected function getFullPath(): string;

    protected function getModelReplacements(): array
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

    protected function replacePatternsInTheStub(array $replacements, string $type): array|string|null
    {
        return preg_replace(
            self::PATTERNS,
            $replacements,
            $this->getStubContent($type)
        );
    }

    protected function getStubContent(string $stubName): string
    {
        return file_get_contents(__DIR__ . "/../Stubs/{$stubName}.stub");
    }

    protected function removeTags(string $string): string
    {
        $result = $string;

        foreach (self::TAGS as $option) {
            $result = $this->removeTag(
                $result,
                $this->options[$option],
                $option
            );
        }

        return $result;
    }

    protected function removeTag(string $string, $condition, string $tag): string
    {
        $pattern = $condition
            ? "/@if\(\'{$tag}\'\)|@endif\(\'{$tag}\'\)/"
            : "/@if\(\'{$tag}\'\)((?>[^@]++))*@endif\(\'{$tag}\'\)/";

        return preg_replace($pattern, '', $string);
    }

    protected function createDirectory(): void
    {
        app(Filesystem::class)->makeDirectory($this->getFolder(), 0777, true, true);
    }

    protected function saveContentInTheFilePath(): void
    {
        file_put_contents($this->getFullPath(), $this->parseStub($this->getStub()));
    }
}
