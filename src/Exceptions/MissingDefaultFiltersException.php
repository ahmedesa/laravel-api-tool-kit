<?php

namespace Essa\APIToolKit\Exceptions;

use Exception;

class MissingDefaultFiltersException extends Exception
{
    protected $message = 'Please add the default_filters property to the model.';
}