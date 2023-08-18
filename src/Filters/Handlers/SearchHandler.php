<?php

namespace Essa\APIToolKit\Filters\Handlers;

use Closure;
use Essa\APIToolKit\Filters\Contracts\QueryFiltersHandlerInterface;
use Essa\APIToolKit\Filters\DTO\QueryFiltersOptionsDTO;
use Illuminate\Database\Eloquent\Builder;

class SearchHandler implements QueryFiltersHandlerInterface
{
    public function handle(QueryFiltersOptionsDTO $queryFiltersOptionsDTO, Closure $next): QueryFiltersOptionsDTO
    {
        $searchValue = $queryFiltersOptionsDTO->getFiltersDTO()->getSearch();

        if (null === $searchValue) {
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

        $builder->where(function (Builder $query) use ($searchValue, $columns, $relationColumns): void {
            foreach ($columns as $key => $column) {
                $clause = $this->getWhereFunction($key);
                $query->{$clause}($column, 'LIKE', "%{$searchValue}%");
            }

            foreach ($relationColumns as $relationship => $relativeColumns) {
                $query->orWhereHas($relationship, function (Builder $relationQuery) use ($searchValue, $relativeColumns): void {
                    foreach ($relativeColumns as $key => $column) {
                        $clause = $this->getWhereFunction($key);
                        $relationQuery->{$clause}($column, 'LIKE', "%{$searchValue}%");
                    }
                });
            }
        });
    }

    private function getWhereFunction(int $key): string
    {
        return 0 === $key ? 'where' : 'orWhere';
    }
}
