<?php

namespace Essa\APIToolKit\Tests\Mocks;

use Essa\APIToolKit\Filters\QueryFilters;
use Essa\APIToolKit\Traits\DateFilter;
use Essa\APIToolKit\Traits\TimeFilter;

class TestModelFilters extends QueryFilters
{
    use DateFilter;
    use TimeFilter;

    protected array $columnSearch = ['name'];
    protected array $allowedFilters = ['id'];
}
