<?php

namespace Essa\APIToolKit\Filters;

use Essa\APIToolKit\DTO\FiltersDTO;
use Illuminate\Database\Eloquent\Builder;

class QueryFilters
{
    protected FiltersDTO $filtersDTO;

    protected Builder $builder;

    protected array $allowedFilters = [];
    protected array $allowedSorts = [];
    protected array $allowedIncludes = [];
    protected array $columnSearch = [];
    protected array $relationSearch = [];

    private array $operations = [
        'applyFilters',
        'applyIncludes',
        'applySorts',
        'applySearch',
    ];

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        if (method_exists($this, 'before')) {
            $this->before();
        }

        foreach ($this->operations as $operation) {
            $this->$operation();
        }

        return $this->builder;
    }

    public function getFiltersDTO(): FiltersDTO
    {
        return $this->filtersDTO;
    }

    public function setFiltersDTO(FiltersDTO $filtersDTO): void
    {
        $this->filtersDTO = $filtersDTO;
    }

    private function applyFilters(): void
    {
        foreach ($this->getFiltersDTO()->getFilters() as $name => $value) {
            if (method_exists($this, $name)) {
                $this->$name($value);
            }

            if (in_array($name, $this->allowedFilters)) {
                $value = explode(',', $value);

                if (count($value) > 1) {
                    $this->builder->whereIn($name, $value);

                    return;
                }

                $this->builder->where($name, '=', $value);
            }
        }
    }

    private function applyIncludes(): void
    {
        $includes = array_intersect($this->getFiltersDTO()->getIncludes(), $this->allowedIncludes);

        $this->builder->with($includes);
    }

    private function applySorts(): void
    {
        if ($this->getFiltersDTO()->getSorts() != null) {
            $first_sort = explode(',', $this->getFiltersDTO()->getSorts())[0];

            $value = ltrim($first_sort, '-');

            if (in_array($value, $this->allowedSorts)) {
                $this->builder->orderBy($value, $this->getDirection($first_sort));
            }
        }
    }

    private function getDirection(string $sort): string
    {
        return strpos($sort, '-') === 0 ? 'desc' : 'asc';
    }

    private function applySearch(): void
    {
        if (is_null($this->getFiltersDTO()->getSearch())) {
            return;
        }

        $keyword = $this->getFiltersDTO()->getSearch();

        $columns = $this->columnSearch;

        $this->builder->where(function ($query) use ($keyword, $columns) {
            if (count($columns) > 0) {
                foreach ($columns as $key => $column) {
                    $clause = $key == 0 ? 'where' : 'orWhere';
                    $query->$clause($column, 'LIKE', "%{$keyword}%");
                }
            }
            $this->searchByRelationship($query, $keyword);
        });
    }

    private function searchByRelationship(Builder $query, string $keyword): void
    {
        $relativeTables = $this->relationSearch;

        foreach ($relativeTables as $relationship => $relativeColumns) {
            $query->orWhereHas($relationship, function ($relationQuery) use ($keyword, $relativeColumns) {
                foreach ($relativeColumns as $key => $column) {
                    $clause = $key == 0 ? 'where' : 'orWhere';
                    $relationQuery->$clause($column, 'LIKE', "%{$keyword}%");
                }
            });
        }
    }
}
