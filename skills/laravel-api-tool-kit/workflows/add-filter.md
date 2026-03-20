# Workflow: Add Filtering to an Existing Model

Use this when an existing model needs filtering, sorting, or search added to its list endpoint.

---

## Step 0 — Detect Project Structure

```bash
ls app/
```

- `Domain/` present → **DDD**: filters live in `app/Domain/{Model}/Filters/{Model}Filters.php`
- No `Domain/` → **Standard**: filters live in `app/Filters/{Model}Filters.php`

Check the existing model file to confirm — it will have `protected string $default_filters = ...` pointing to the correct namespace if a filter already exists.

---

## Step 1 — Check if a Filter class already exists

Standard: look for `app/Filters/{Model}Filters.php`
DDD: look for `app/Domain/{Model}/Filters/{Model}Filters.php`

- If it exists → go to Step 3
- If it does not → go to Step 2

---

## Step 2 — Create the Filter class

Follow `rules/filters.md` for the correct structure.

```
Standard: app/Filters/CarFilters.php
DDD:      app/Domain/Car/Filters/CarFilters.php
```

---

## Step 3 — Bind the Filter to the Model

Open the Model and ensure:

```php
use App\Filters\CarFilters;
use Essa\APIToolKit\Filters\Filterable;

class Car extends Model
{
    use Filterable;

    protected string $default_filters = CarFilters::class;
}
```

Both `use Filterable` and `protected string $default_filters` are required.

---

## Step 4 — Populate the Filter class

Based on the requirement, fill in the appropriate arrays:

**Add equality filters** (`?color=red&is_active=1`):
```php
protected array $allowedFilters = ['color', 'is_active', 'user_id'];
```

**Add sort columns** (`?sorts=name` or `?sorts=-created_at`):
```php
protected array $allowedSorts = ['name', 'created_at'];
```

**Add eager-load includes** (`?includes=user,tags`):
```php
protected array $allowedIncludes = ['user', 'tags'];
```

**Add text search columns** (`?search=keyword`):
```php
protected array $columnSearch = ['name', 'description'];
```

**Add relationship search** (`?search=keyword`):
```php
protected array $relationSearch = [
    'user' => ['first_name', 'last_name'],
];
```

**Add a custom filter** for non-trivial conditions:
```php
// ?year=2023
public function year(string $term): void
{
    $this->builder->whereYear('manufactured_at', $term);
}
```

---

## Step 5 — Update the Controller

Open the controller's `index` method and replace any manual query building with:

```php
public function index(): AnonymousResourceCollection
{
    return CarResource::collection(Car::useFilters()->dynamicPaginate());
}
```

Do NOT hardcode `->with(['user', 'tags'])` here if those relationships are in `$allowedIncludes`. The client requests them via `?includes=user,tags` and the filter class handles eager loading automatically. Hardcoding `with()` always loads relations even when not needed.

---

## Checklist

- [ ] `Filterable` trait is on the model
- [ ] `$default_filters` is bound on the model
- [ ] Filter class `$allowedFilters` / `$allowedSorts` are populated
- [ ] Controller uses `useFilters()->dynamicPaginate()` — not `->get()` or `->paginate()`
- [ ] Custom filter methods operate on `$this->builder` — they return nothing
- [ ] No filter logic remains in the controller method
