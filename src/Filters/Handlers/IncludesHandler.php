<?php

namespace Essa\APIToolKit\Filters\Handlers;

use Closure;
use Essa\APIToolKit\Filters\Contracts\QueryFiltersHandlerInterface;
use Essa\APIToolKit\Filters\DTO\QueryFiltersOptionsDTO;

class IncludesHandler implements QueryFiltersHandlerInterface
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
