<?php

namespace Essa\APIToolKit\Filters\DTO;

use Illuminate\Http\Request;

class FiltersDTO
{
    public function __construct(
        private ?string $sorts = null,
        private ?array $filters = null,
        private ?array $includes = null,
        private ?string $search = null
    ) {
    }

    public static function buildFromRequest(Request $request): FiltersDTO
    {
        return new self(
            $request->all('sorts')['sorts'],
            $request->except('includes', 'sorts'),
            explode(',', $request->get('includes')),
            $request->all('search')['search']
        );
    }

    public function getSorts(): ?string
    {
        return $this->sorts;
    }

    public function setSorts(?string $sorts): void
    {
        $this->sorts = $sorts;
    }

    public function getFilters(): ?array
    {
        return $this->filters;
    }

    public function setFilters(?array $filters): void
    {
        $this->filters = $filters;
    }

    public function getIncludes(): ?array
    {
        return $this->includes;
    }

    public function setIncludes(?array $includes): void
    {
        $this->includes = $includes;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): void
    {
        $this->search = $search;
    }

    public function getFilter(string $key): ?string
    {
        if ( ! array_key_exists($key, $this->filters)) {
            return null;
        }

        return $this->filters[$key];
    }
}
