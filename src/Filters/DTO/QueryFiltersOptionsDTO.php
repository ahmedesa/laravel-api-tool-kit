<?php

namespace Essa\APIToolKit\Filters\DTO;

use Illuminate\Database\Eloquent\Builder;

class QueryFiltersOptionsDTO
{
    public function __construct(
        private Builder    $builder,
        private FiltersDTO $filtersDTO,
        private array      $allowedFilters = [],
        private array      $allowedSorts = [],
        private array      $allowedIncludes = [],
        private array      $columnSearch = [],
        private array      $relationSearch = []
    ) {
    }

    public function getAllowedFilters(): array
    {
        return $this->allowedFilters;
    }

    public function getAllowedSorts(): array
    {
        return $this->allowedSorts;
    }

    public function getAllowedIncludes(): array
    {
        return $this->allowedIncludes;
    }

    public function getColumnSearch(): array
    {
        return $this->columnSearch;
    }

    public function getRelationSearch(): array
    {
        return $this->relationSearch;
    }

    public function setAllowedFilters(array $allowedFilters): self
    {
        $this->allowedFilters = $allowedFilters;

        return $this;
    }

    public function setAllowedSorts(array $allowedSorts): self
    {
        $this->allowedSorts = $allowedSorts;

        return $this;
    }

    public function setAllowedIncludes(array $allowedIncludes): self
    {
        $this->allowedIncludes = $allowedIncludes;

        return $this;
    }

    public function setColumnSearch(array $columnSearch): self
    {
        $this->columnSearch = $columnSearch;

        return $this;
    }

    public function setRelationSearch(array $relationSearch): self
    {
        $this->relationSearch = $relationSearch;

        return $this;
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
}
