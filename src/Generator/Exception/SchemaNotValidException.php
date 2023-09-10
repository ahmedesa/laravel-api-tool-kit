<?php

namespace Essa\APIToolKit\Generator\Exception;

use Exception;

class SchemaNotValidException extends Exception
{
    public function __construct()
    {
        parent::__construct('your schema is not valid');
    }
}
