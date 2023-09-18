<?php

namespace Essa\APIToolKit\Filters;

use Essa\APIToolKit\Filters\DTO\FiltersDTO;
use Essa\APIToolKit\Filters\Exceptions\MissingDefaultFiltersException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * @method static Builder useFilters(string $filterClass = null, ?FiltersDTO $filteredDTO = null) filter class
 */
trait Filterable
{
    public function scopeUseFilters(
        Builder $query,
        ?string $filterClass = null,
        ?FiltersDTO $filteredDTO = null
    ): Builder {
        if ( ! property_exists($this, 'default_filters') && null === $filterClass) {
            throw new MissingDefaultFiltersException();
        }

        $filteredDTO ??= FiltersDTO::buildFromRequest(app(Request::class));

        /** @var QueryFilters $class */
        $class = app($filterClass ?? $this->default_filters);

        return $class->setFiltersDTO($filteredDTO)
            ->setBuilder($query)
            ->apply();
    }
}
