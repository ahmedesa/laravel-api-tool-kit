<?php

namespace essa\APIGenerator\Generator;

use Illuminate\Support\Str;

trait FileManger
{
    protected function getTemplate($type)
    {
        return str_replace(
            [
                'Dummy',
                'Dummies',
                'dummy',
                'dummies',
            ],
            [
                $this->model,
                Str::plural($this->model),
                lcfirst($this->model),
                lcfirst(Str::plural($this->model)),
            ],
            $this->getStubs($type)
        );
    }

    protected function getStubs($type)
    {
        return file_get_contents(__DIR__ . '/../Stubs/' . $type . ".stub");
    }
}