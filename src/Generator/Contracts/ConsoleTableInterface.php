<?php

namespace Essa\APIToolKit\Generator\Contracts;

use Essa\APIToolKit\Generator\DTOs\ApiGenerationCommandInputs;
use Essa\APIToolKit\Generator\DTOs\TableDate;

interface ConsoleTableInterface
{
    public function generate(ApiGenerationCommandInputs $apiGenerationCommandInputs): TableDate;
}
