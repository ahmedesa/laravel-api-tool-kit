<?php

namespace Essa\APIToolKit\Generator\Contracts;

use Essa\APIToolKit\Generator\ApiGenerationCommandInputs;
use Essa\APIToolKit\Generator\TableDate;

interface ConsoleTableInterface
{
    public function generate(ApiGenerationCommandInputs $apiGenerationCommandInputs): TableDate;
}
