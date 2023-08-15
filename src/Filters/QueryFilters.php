<?php

namespace Essa\APIToolKit\Filters;

use Essa\APIToolKit\Filters\DTO\FiltersDTO;
use Essa\APIToolKit\Filters\DTO\QueryFiltersOptionsDTO;
use Essa\APIToolKit\Filters\Operations\FilterOperation;
use Essa\APIToolKit\Filters\Operations\IncludeOperation;
use Essa\APIToolKit\Filters\Operations\SearchOperation;
use Essa\APIToolKit\Filters\Operations\SortOperation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

class QueryFilters
{
    protected FiltersDTO $filtersDTO;

    protected Builder $builder;

    protected array $allowedFilters = [];
    protected array $allowedSorts = [];
    protected array $allowedIncludes = [];
    protected array $columnSearch = [];
    protected array $relationSearch = [];

    public function apply(): Builder
    {
        $this->runBeforeMethod();

        $this->applyCustomFilters();

        return app(Pipeline::class)
            ->send(new QueryFiltersOptionsDTO(
                $this->builder,
                $this->filtersDTO,
                $this->allowedFilters,
                $this->allowedSorts,
                $this->allowedIncludes,
                $this->columnSearch,
                $this->relationSearch
            ))
            ->through([
                FilterOperation::class,
                SortOperation::class,
                IncludeOperation::class,
                SearchOperation::class,
            ])
            ->thenReturn()
            ->getBuilder();
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function setBuilder(Builder $builder): self
    {
        $this->builder = $builder;

        return $this;
    }

    public function getFiltersDTO(): FiltersDTO
    {
        return $this->filtersDTO;
    }

    public function setFiltersDTO(FiltersDTO $filtersDTO): self
    {
        $this->filtersDTO = $filtersDTO;

        return $this;
    }

    protected function runBeforeMethod(): void
    {
        if (method_exists($this, 'before')) {
            $this->before();
        }
    }

    protected function applyCustomFilters(): void
    {
        foreach ($this->getFiltersDTO()->getFilters() as $name => $value) {
            if (method_exists($this, $name)) {
                $this->$name($value);
            }
        }
    }
}
