<?php

namespace Essa\APIToolKit\Generator\Commands;

use Essa\APIToolKit\Generator\ApiGenerationCommandInputs;
use Essa\APIToolKit\Generator\Contracts\GeneratorCommandInterface;
use Essa\APIToolKit\Generator\Contracts\HasDynamicContent;
use Essa\APIToolKit\Generator\Contracts\PathResolverInterface;
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
    protected string $type;
    protected ApiGenerationCommandInputs $apiGenerationCommandInputs;

    public function __construct(protected Filesystem $filesystem)
    {
    }

    public function run(ApiGenerationCommandInputs $apiGenerationCommandInputs): void
    {
        $this->apiGenerationCommandInputs = $apiGenerationCommandInputs;

        if ( ! file_exists($this->getOutputFilePath()->folderPath())) {
            $this->createFolder();
        }

        $this->saveContentToFile();
    }

    abstract protected function getStubName(): string;

    protected function getOutputFilePath(): PathResolverInterface
    {
        $pathResolverClass = config("api-tool-kit-internal.api_generators.options.{$this->type}.path_resolver");

        return new $pathResolverClass($this->apiGenerationCommandInputs->getModel());
    }

    protected function createFolder(): void
    {
        $this->filesystem
            ->makeDirectory(
                path: $this->getOutputFilePath()->folderPath(),
                mode: 0777,
                recursive: true,
                force: true
            );
    }

    protected function saveContentToFile(): void
    {
        file_put_contents(
            filename: $this->getOutputFilePath()->getFullPath(),
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

        if ($this instanceof HasDynamicContent) {
            $replacements = array_merge($replacements, $this->getContent());
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
                $this->apiGenerationCommandInputs->getUserChoices()[$option],
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
            '{{Dummy}}' => $this->apiGenerationCommandInputs->getModel(),
            '{{Dummies}}' => Str::plural($this->apiGenerationCommandInputs->getModel()),
            '{{dummy}}' => lcfirst($this->apiGenerationCommandInputs->getModel()),
            '{{dummies}}' => lcfirst(Str::plural($this->apiGenerationCommandInputs->getModel())),
        ];
    }
}
