<?php

namespace Essa\APIToolKit\Filters;

use Essa\APIToolKit\DTO\FiltersDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * @method static useFilters(string $filterClass = null, ?FiltersDTO $filteredDTO = null) filter class
 */
trait Filterable
{
    public function scopeUseFilters(
        Builder     $query,
        string      $filterClass = null,
        ?FiltersDTO $filteredDTO = null
    ): Builder
    {
        if (!property_exists($this, 'default_filters') && is_null($filterClass)) {
            throw new \Exception('please add default_filters property to the model');
        }

        $filteredDTO = $filteredDTO ?? FiltersDTO::buildFromRequest(app(Request::class));

        /** @var QueryFilters $class */
        $class = app($filterClass ?? $this->default_filters);

        $class->setFiltersDTO($filteredDTO);

        return $class->apply($query);
    }
}