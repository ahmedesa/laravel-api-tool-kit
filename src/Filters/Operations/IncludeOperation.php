<?php

namespace Essa\APIToolKit\Filters\Operations;

use Closure;
use Essa\APIToolKit\Filters\Contracts\FilterOperationInterface;
use Essa\APIToolKit\Filters\DTO\QueryFiltersOptionsDTO;

class IncludeOperation implements FilterOperationInterface
{
    public function handle(QueryFiltersOptionsDTO $queryFiltersOptionsDTO, Closure $next): QueryFiltersOptionsDTO
    {
        $includes = array_intersect(
            $queryFiltersOptionsDTO->getFiltersDTO()->getIncludes(),
            $queryFiltersOptionsDTO->getAllowedIncludes()
        );

        $queryFiltersOptionsDTO->getBuilder()->with($includes);

        return $next($queryFiltersOptionsDTO);
    }
}
