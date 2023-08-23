<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

abstract class BaseGeneratorCommand
{
    protected const TAGS = [
        'soft-delete',
        'request',
        'resource',
        'filter',
    ];
    protected ?array $schema;

    protected array $options;
    protected string $model;

    public function __construct(private Filesystem $filesystem)
    {
    }

    public function run(): void
    {
        if ( ! file_exists($this->getOutputFolder())) {
            $this->createFolder();
        }

        $this->saveContentToFile();
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function setSchema(?array $schema): self
    {
        $this->schema = $schema;

        return $this;
    }
    abstract protected function getStubName(): string;

    abstract protected function getOutputFolder(): string;

    abstract protected function getOutputFilePath(): string;

    protected function createFolder(): void
    {
        $this->filesystem
            ->makeDirectory(
                path: $this->getOutputFolder(),
                mode: 0777,
                recursive: true,
                force: true
            );
    }

    protected function saveContentToFile(): void
    {
        file_put_contents($this->getOutputFilePath(), $this->parseStub($this->getStubName()));
    }

    protected function parseStub(string $type): string
    {
        $output = $this->replacePatternsInTheStub($type);

        return $this->removeTags($output);
    }

    protected function replacePatternsInTheStub(string $type): array|string|null
    {
        $replacements = $this->getPlaceholderReplacements();

        if ($this instanceof SchemaReplacementDataProvider && $this->schema) {
            $replacements = array_merge($replacements, $this->getSchemaReplacements());
        }

        return strtr(
            $this->getStubContent($type),
            $replacements
        );
    }

    protected function removeTags(string $content): string
    {
        $processedContent = $content;

        foreach (self::TAGS as $option) {
            $processedContent = $this->removeTagBlock(
                $processedContent,
                $this->options[$option],
                $option
            );
        }

        return $processedContent;
    }

    protected function getStubContent(string $stubName): string
    {
        return file_get_contents(__DIR__ . "/../../Stubs/{$stubName}.stub");
    }

    protected function removeTagBlock(string $string, $condition, string $tag): string
    {
        $pattern = $condition
            ? "/@if\(\'{$tag}\'\)|@endif\(\'{$tag}\'\)/"
            : "/@if\(\'{$tag}\'\)((?>[^@]++))*@endif\(\'{$tag}\'\)/";

        return preg_replace($pattern, '', $string);
    }

    protected function getPlaceholderReplacements(): array
    {
        return [
            'Dummy' => $this->model,
            'Dummies' => Str::plural($this->model),
            'dummy' => lcfirst($this->model),
            'dummies' => lcfirst(Str::plural($this->model)),
        ];
    }
}
