<?php

namespace Essa\APIToolKit\Generator\DTOs;

class CommandInfo
{
    public function __construct(
        public string $type,
        public string $name,
        public array  $options
    ) {
    }
}
