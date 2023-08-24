<?php

namespace Essa\APIToolKit\Generator\Contracts;

use Essa\APIToolKit\Generator\DTOs\TableDate;

interface ConsoleTableInterface
{
    public function generate(): TableDate;
}
