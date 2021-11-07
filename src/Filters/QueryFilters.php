<?php
namespace essa\APIToolKit\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class QueryFilters
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Builder
     */
    protected Builder $builder;

    protected array $allowedFilters  = [];
    protected array $allowedSorts    = [];
    protected array $allowedIncludes = [];
    protected array $columnSearch    = [];
    protected array $relationSearch  = [];

    public function __construct()
    {
        $this->request = request();
    }

    private array $operations = [
        'applyFilters',
        'applyIncludes',
        'applySorts',
        'applySearch',
    ];

    /**
     * @param Builder $builder
     * @return Builder
     */
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

    protected function filters(): array
    {
        return $this->request->except('includes', 'sorts');
    }

    private function includes(): array
    {
        return explode(',', $this->request->get('includes'));
    }

    private function sorts()
    {
        return $this->request->all('sorts')['sorts'];
    }

    private function search()
    {
        return $this->request->all('search')['search'];
    }

    private function applyFilters(): void
    {
        foreach ($this->filters() as $name => $value) {
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
        $includes = array_intersect($this->includes(), $this->allowedIncludes);

        $this->builder->with($includes);
    }

    private function applySorts(): void
    {
        if ($this->sorts() != null) {
            $first_sort = explode(',', $this->sorts())[0];

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

    private function applySearch()
    {
        if (is_null($this->search())) {
            return;
        }
        $keyword = $this->search();

        $columns = $this->columnSearch;

        $this->builder->where(function ($query) use ($keyword, $columns) {
            if (count($columns) > 0) {
                foreach ($columns as $key => $column) {
                    $clause = $key == 0 ? 'where' : 'orWhere';
                    $query->$clause($column, "LIKE", "%$keyword%");
                }
            }
            $this->searchByRelationship($query, $keyword);
        });
    }

    private function searchByRelationship($query, $keyword)
    {
        $relativeTables = $this->relationSearch;

        foreach ($relativeTables as $relationship => $relativeColumns) {
            $query->orWhereHas($relationship, function ($relationQuery) use ($keyword, $relativeColumns) {
                foreach ($relativeColumns as $key => $column) {
                    $clause = $key == 0 ? 'where' : 'orWhere';
                    $relationQuery->$clause($column, "LIKE", "%$keyword%");
                }
            });
        }
    }
}
