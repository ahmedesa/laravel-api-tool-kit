<?php

namespace Essa\APIToolKit\Filters\Contracts;

use Closure;
use Essa\APIToolKit\Filters\DTO\QueryFiltersOptionsDTO;

interface FilterOperationInterface
{
    public function handle(QueryFiltersOptionsDTO $queryFiltersOptionsDTO, Closure $next): QueryFiltersOptionsDTO;
}
