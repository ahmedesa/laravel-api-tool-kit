<?php

namespace Essa\APIToolKit\Filters\Handlers;

use Closure;
use Essa\APIToolKit\Filters\Contracts\QueryFiltersHandlerInterface;
use Essa\APIToolKit\Filters\DTO\QueryFiltersOptionsDTO;

class FiltersHandler implements QueryFiltersHandlerInterface
{
    public function handle(QueryFiltersOptionsDTO $queryFiltersOptionsDTO, Closure $next): QueryFiltersOptionsDTO
    {
        $filters = $queryFiltersOptionsDTO->getFiltersDTO()->getFilters();
        $allowedFilters = $queryFiltersOptionsDTO->getAllowedFilters();
        $builder = $queryFiltersOptionsDTO->getBuilder();

        foreach ($filters as $name => $value) {
            if (in_array($name, $allowedFilters)) {
                $value = explode(',', $value);

                if (count($value) > 1) {
                    $builder->whereIn($name, $value);
                } else {
                    $builder->where($name, '=', $value[0]);
                }
            }
        }

        return $next($queryFiltersOptionsDTO);
    }
}
