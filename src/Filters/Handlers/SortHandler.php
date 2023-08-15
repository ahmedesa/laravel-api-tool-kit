<?php

namespace Essa\APIToolKit\Filters\Handlers;

use Closure;
use Essa\APIToolKit\Filters\Contracts\QueryFiltersHandlerInterface;
use Essa\APIToolKit\Filters\DTO\QueryFiltersOptionsDTO;

class SortHandler implements QueryFiltersHandlerInterface
{
    public function handle(QueryFiltersOptionsDTO $queryFiltersOptionsDTO, Closure $next): QueryFiltersOptionsDTO
    {
        $sorts = $queryFiltersOptionsDTO->getFiltersDTO()->getSorts();
        $builder = $queryFiltersOptionsDTO->getBuilder();

        if (is_null($sorts)) {
            return $next($queryFiltersOptionsDTO);
        }

        $firstSort = explode(',', $sorts)[0];

        $value = ltrim($firstSort, '-');

        if (in_array($value, $queryFiltersOptionsDTO->getAllowedSorts())) {
            $builder->orderBy($value, $this->getDirection($firstSort));

            return $next($queryFiltersOptionsDTO);
        }

        return $next($queryFiltersOptionsDTO);
    }

    private function getDirection(string $sort): string
    {
        return strpos($sort, '-') === 0 ? 'desc' : 'asc';
    }
}
