<?php

namespace Essa\APIToolKit\Tests\Mocks;

use Essa\APIToolKit\Filters\QueryFilters;

class TestModelFilters extends QueryFilters
{
    protected array $columnSearch = ['name'];
    protected array $allowedFilters = ['id'];
}
