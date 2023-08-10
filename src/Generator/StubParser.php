<?php

namespace Essa\APIToolKit\Generator;

use Illuminate\Support\Str;

class StubParser
{
    private string $model;
    private array $options;

    private array $patterns = [
        '/Dummy/',
        '/Dummies/',
        '/dummy/',
        '/dummies/',
    ];

    private array $tags = [
        'soft-delete',
        'request',
        'resource',
        'filter',
    ];

    public function __construct(string $model, array $options)
    {
        $this->model = $model;
        $this->options = $options;
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
        ];
    }

    private function replacePatternsInTheStub(array $replacements, string $type)
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
