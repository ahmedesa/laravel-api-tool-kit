<?php

namespace Essa\APIToolKit\Generator\Handlers;

class CommandInfo
{
    public string $type;
    public string $name;
    public array $options;

    public function __construct(string $type, string $name, array $options)
    {
        $this->type = $type;
        $this->name = $name;
        $this->options = $options;
    }
}
