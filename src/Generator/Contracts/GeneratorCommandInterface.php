<?php

namespace Essa\APIToolKit\Generator\Contracts;

use Essa\APIToolKit\Generator\DTOs\GenerationConfiguration;

interface GeneratorCommandInterface
{
    public function run(GenerationConfiguration $generationConfiguration): void;
}
