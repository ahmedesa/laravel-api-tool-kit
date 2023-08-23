<?php

namespace Essa\APIToolKit\Generator;

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

    public function __construct(
        protected string $model,
        protected array  $options,
        protected ?array $schema = null
    ) {
    }

    public function handle(): void
    {
        if ( ! file_exists($this->getFolder())) {
            $this->createDirectory();
        }

        $this->saveContentInTheFilePath();
    }

    abstract protected function getStub(): string;

    abstract protected function getFolder(): string;

    abstract protected function getFullPath(): string;

    protected function createDirectory(): void
    {
        app(Filesystem::class)
            ->makeDirectory(
                path: $this->getFolder(),
                mode: 0777,
                recursive: true,
                force: true
            );
    }

    protected function saveContentInTheFilePath(): void
    {
        file_put_contents($this->getFullPath(), $this->parseStub($this->getStub()));
    }

    protected function parseStub(string $type): string
    {
        $output = $this->replacePatternsInTheStub($type);

        return $this->removeTags($output);
    }

    protected function replacePatternsInTheStub(string $type): array|string|null
    {
        $replacements = $this->replacementPatterns();

        if (method_exists($this, 'schemaReplacements') && $this->schema) {
            $replacements = array_merge($replacements, $this->schemaReplacements());
        }

        return strtr(
            $this->getStubContent($type),
            $replacements
        );
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

    protected function getStubContent(string $stubName): string
    {
        return file_get_contents(__DIR__ . "/../Stubs/{$stubName}.stub");
    }

    protected function removeTag(string $string, $condition, string $tag): string
    {
        $pattern = $condition
            ? "/@if\(\'{$tag}\'\)|@endif\(\'{$tag}\'\)/"
            : "/@if\(\'{$tag}\'\)((?>[^@]++))*@endif\(\'{$tag}\'\)/";

        return preg_replace($pattern, '', $string);
    }

    protected function replacementPatterns(): array
    {
        return [
            'Dummy' => $this->model,
            'Dummies' => Str::plural($this->model),
            'dummy' => lcfirst($this->model),
            'dummies' => lcfirst(Str::plural($this->model)),
        ];
    }
}
