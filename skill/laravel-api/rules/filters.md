# Filters

## Rules

- MUST create a `QueryFilters` subclass for every filterable model
- MUST bind it to the model via `protected string $default_filters`
- MUST use `useFilters()->dynamicPaginate()` on index endpoints — NEVER build query conditions in controllers
- Custom filter methods MUST operate on `$this->builder` and return `void`
- NEVER hardcode `auth()->id()` inside a scope called from a filter method

## Model Binding

```php
use App\Filters\CarFilters;
use Essa\APIToolKit\Filters\Filterable;

class Car extends Model
{
    use Filterable;

    protected string $default_filters = CarFilters::class;
}
```

## Filter Class

```php
use Essa\APIToolKit\Filters\QueryFilters;

class CarFilters extends QueryFilters
{
    protected array $allowedFilters  = ['color', 'model_id', 'is_active'];
    protected array $allowedSorts    = ['created_at', 'name'];
    protected array $allowedIncludes = ['brand', 'owner'];
    protected array $columnSearch    = ['name', 'description'];
    protected array $relationSearch  = [
        'owner' => ['first_name', 'last_name'],
    ];

    // Custom: ?year=2023
    public function year(string $term): void
    {
        $this->builder->whereYear('manufactured_at', $term);
    }
}
```

## URL Parameters

| Parameter | Example | Effect |
|-----------|---------|--------|
| `search` | `?search=toyota` | LIKE search on `$columnSearch` columns |
| `sorts` | `?sorts=name` / `?sorts=-name` | Sort asc / desc |
| `includes` | `?includes=brand,owner` | Eager load |
| Any `$allowedFilters` key | `?color=red` | WHERE equality |
| Any custom method name | `?year=2023` | Calls `year()` on the filter class |

## Date / Time Range Traits

```php
use Essa\APIToolKit\Traits\DateFilter; // adds ?from_date= and ?to_date=
use Essa\APIToolKit\Traits\TimeFilter; // adds ?from_time= and ?to_time=

class CarFilters extends QueryFilters
{
    use DateFilter;
    use TimeFilter;
}
```

## Controller Usage

```php
public function index(): AnonymousResourceCollection
{
    $cars = Car::with(['brand', 'owner'])->useFilters()->dynamicPaginate();
    return CarResource::collection($cars);
}
```
