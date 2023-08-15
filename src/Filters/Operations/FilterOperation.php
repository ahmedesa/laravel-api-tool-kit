<?php

namespace Essa\APIToolKit\Filters\Operations;

use Closure;
use Essa\APIToolKit\Filters\Contracts\FilterOperationInterface;
use Essa\APIToolKit\Filters\DTO\QueryFiltersOptionsDTO;

class FilterOperation implements FilterOperationInterface
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
