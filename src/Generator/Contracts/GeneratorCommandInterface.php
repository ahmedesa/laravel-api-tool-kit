<?php

namespace Essa\APIToolKit\Generator\Contracts;

use Essa\APIToolKit\Generator\DTOs\ApiGenerationCommandInputs;

interface GeneratorCommandInterface
{
    public function run(ApiGenerationCommandInputs $apiGenerationCommandInputs): void;
}
