# Pagination

## Rules

- MUST use `dynamicPaginate()` on all list endpoints — NEVER `->paginate()` or `->get()`
- MUST call `useFilters()` before `dynamicPaginate()` on filterable models
- NEVER hardcode a page size in the controller

## Usage

```php
$cars = Car::useFilters()->dynamicPaginate();
$cars = Car::with(['brand'])->useFilters()->dynamicPaginate();
```

## Client Parameters

| Parameter | Example | Effect |
|-----------|---------|--------|
| `per_page` | `?per_page=25` | Items per page |
| `page` | `?page=2` | Page number |
| `pagination` | `?pagination=none` | Disable pagination, return all |
