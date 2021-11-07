<?php

namespace essa\APIToolKit\Generator;

use Illuminate\Support\Str;

trait FileManger
{
    protected function getTemplate($type)
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

        $output = $this->removeTags(
            $output,
            $this->option('soft-delete'),
            'soft_delete'
        );

        $output = $this->removeTags(
            $output,
            $this->option('request'),
            'request'
        );

        $output = $this->removeTags(
            $output,
            $this->option('resource'),
            'resource'
        );

        $output = $this->removeTags(
            $output,
            $this->option('filter'),
            'filters'
        );

        return $output;
    }

    private function getStubs($type)
    {
        return file_get_contents(__DIR__ . '/../Stubs/' . $type . ".stub");
    }

    public function removeTags($string, $condition, $tag)
    {
        $pattern = $condition
            ? "/@if\(\'$tag\'\)|@endif\(\'$tag\'\)/"
            : "/@if\(\'$tag\'\)((?>[^@]++))*@endif\(\'$tag\'\)/";

        return preg_replace($pattern, '', $string);
    }
}
