<?php

namespace Essa\APIToolKit\DTO;

use Illuminate\Http\Request;

class FiltersDTO
{
    private ?string $sorts;
    private ?array $filters;
    private ?array $includes;
    private ?string $search;

    public function __construct(
        ?string $sorts = null,
        ?array $filters = null,
        ?array $includes = null,
        ?string $search = null
    ) {
        $this->sorts = $sorts;
        $this->filters = $filters;
        $this->includes = $includes;
        $this->search = $search;
    }

    public static function buildFromRequest(Request $request): FiltersDTO
    {
        return new static(
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

    public function getFilter(string $key)
    {
        if (! array_key_exists($key, $this->filters)) {
            return null;
        }

        return $this->filters[$key];
    }
}
