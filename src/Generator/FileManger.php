<?php

namespace essa\APIToolKit\Generator;

use Illuminate\Support\Str;

trait FileManger
{
    protected function getTemplate(string $type): string
    {
        $patterns = [
            '/Dummy/',
            '/Dummies/',
            '/dummy/',
            '/dummies/',
        ];

        $replacements = [
            $this->model,
            Str::plural($this->model),
            lcfirst($this->model),
            lcfirst(Str::plural($this->model)),
        ];

        $output = preg_replace(
            $patterns,
            $replacements,
            $this->getStubs($type)
        );

        return $this->removeTags($output, [
            'soft-delete',
            'request',
            'resource',
            'filter',
        ]);
    }

    private function getStubs(string $type): string
    {
        return file_get_contents(__DIR__ . '/../Stubs/' . $type . ".stub");
    }

    private function removeTags(string $string, array $options): string
    {
        $result = $string;

        foreach ($options as $option) {
            $result = $this->removeTag(
                $result,
                $this->option($option),
                $option
            );
        }

        return $result;
    }

    private function removeTag(string $string, $condition, string $tag): string
    {
        $pattern = $condition
            ? "/@if\(\'$tag\'\)|@endif\(\'$tag\'\)/"
            : "/@if\(\'$tag\'\)((?>[^@]++))*@endif\(\'$tag\'\)/";

        return preg_replace($pattern, '', $string);
    }
}
