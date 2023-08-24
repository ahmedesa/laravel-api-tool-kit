<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\Contracts\GeneratorCommandInterface;
use Essa\APIToolKit\Generator\Contracts\SchemaReplacementDataProvider;
use Essa\APIToolKit\Generator\DTOs\GenerationConfiguration;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

abstract class GeneratorCommand implements GeneratorCommandInterface
{
    protected const TAGS = [
        'soft-delete',
        'request',
        'resource',
        'filter',
    ];
    protected GenerationConfiguration $generationConfiguration;

    public function __construct(private Filesystem $filesystem)
    {
    }

    public function run(GenerationConfiguration $generationConfiguration): void
    {
        $this->generationConfiguration = $generationConfiguration;

        if ( ! file_exists($this->getOutputFolderPath())) {
            $this->createFolder();
        }

        $this->saveContentToFile();
    }

    abstract protected function getStubName(): string;

    abstract protected function getOutputFolderPath(): string;

    abstract protected function getOutputFileName(): string;

    protected function createFolder(): void
    {
        $this->filesystem
            ->makeDirectory(
                path: $this->getOutputFolderPath(),
                mode: 0777,
                recursive: true,
                force: true
            );
    }

    protected function saveContentToFile(): void
    {
        file_put_contents(
            filename: $this->getOutputFolderPath() . "/" . $this->getOutputFileName(),
            data: $this->parseStub($this->getStubName())
        );
    }

    protected function parseStub(string $type): string
    {
        $output = $this->replacePatternsInTheStub($type);

        return $this->removeTags($output);
    }

    protected function replacePatternsInTheStub(string $type): array|string|null
    {
        $replacements = $this->getPlaceholderReplacements();

        if ($this instanceof SchemaReplacementDataProvider) {
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
                $this->generationConfiguration->getUserChoices()[$option],
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
            'Dummy' => $this->generationConfiguration->getModel(),
            'Dummies' => Str::plural($this->generationConfiguration->getModel()),
            'dummy' => lcfirst($this->generationConfiguration->getModel()),
            'dummies' => lcfirst(Str::plural($this->generationConfiguration->getModel())),
        ];
    }
}
