<?php

namespace Essa\APIToolKit\Filters\Operations;

use Closure;
use Essa\APIToolKit\Filters\Contracts\FilterOperationInterface;
use Essa\APIToolKit\Filters\DTO\QueryFiltersOptionsDTO;
use Illuminate\Database\Eloquent\Builder;

class SearchOperation implements FilterOperationInterface
{
    public function handle(QueryFiltersOptionsDTO $queryFiltersOptionsDTO, Closure $next): QueryFiltersOptionsDTO
    {
        $searchValue = $queryFiltersOptionsDTO->getFiltersDTO()->getSearch();

        if (is_null($searchValue)) {
            return $next($queryFiltersOptionsDTO);
        }

        $this->applySearch($queryFiltersOptionsDTO, $searchValue);

        return $next($queryFiltersOptionsDTO);
    }

    private function applySearch(QueryFiltersOptionsDTO $dataQueryOptionsDTO, string $searchValue): void
    {
        $columns = $dataQueryOptionsDTO->getColumnSearch();
        $relationColumns = $dataQueryOptionsDTO->getRelationSearch();

        $builder = $dataQueryOptionsDTO->getBuilder();

        $builder->where(function (Builder $query) use ($searchValue, $columns, $relationColumns) {
            foreach ($columns as $key => $column) {
                $clause = $this->getWhereFunction($key);
                $query->$clause($column, 'LIKE', "%{$searchValue}%");
            }

            foreach ($relationColumns as $relationship => $relativeColumns) {
                $query->orWhereHas($relationship, function (Builder $relationQuery) use ($searchValue, $relativeColumns) {
                    foreach ($relativeColumns as $key => $column) {
                        $clause = $this->getWhereFunction($key);
                        $relationQuery->$clause($column, 'LIKE', "%{$searchValue}%");
                    }
                });
            }
        });
    }

    private function getWhereFunction(int $key): string
    {
        return $key == 0 ? 'where' : 'orWhere';
    }
}
