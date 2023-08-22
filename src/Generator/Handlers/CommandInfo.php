<?php

namespace Essa\APIToolKit\Generator\Handlers;

class CommandInfo
{
    public function __construct(
        public string $type,
        public string $name,
        public array  $options
    ) {
    }
}
