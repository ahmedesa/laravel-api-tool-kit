# Models

## Fat Models — What Belongs Here

Models SHOULD contain logic about their own data (fat model = good):
- Boolean checks: `$order->isApproved()`, `$user->hasReachedLimit()`
- Calculations from own fields: `$order->getTotalWithTax()`
- Single-step state changes: `$car->toggleActivation()`
- Scopes, accessors, mutators, relationships

Models MUST NOT contain (god object = bad):
- External service calls (emails, file uploads, API calls) — use Actions
- Multi-step workflows with side effects — use Actions
- Operations spanning multiple unrelated models — use Actions

## Rules

- MUST define `$fillable` explicitly — NEVER use `$guarded = []`
- MUST add `$casts` for all booleans, enums, arrays, and dates
- MUST use `Filterable` trait and bind `$default_filters` for any model with a filterable endpoint
- NEVER create a scope for a simple single-column condition — use `where()` directly
- Scopes MUST accept an optional `$userId` — NEVER hardcode `auth()->id()` in a scope (breaks in queues)
- Enable `Model::shouldBeStrict(! app()->isProduction())` in `AppServiceProvider::boot()`

## Structure

```php
class Car extends Model
{
    use HasFactory, Filterable;

    protected string $default_filters = CarFilters::class;

    protected $fillable = ['name', 'color', 'is_active', 'user_id'];

    protected $casts = [
        'is_active' => 'boolean',
        'status'    => CarStatusEnum::class,
    ];
}
```

## Model Method (single-step, own data)

```php
public function toggleActivation(): void
{
    $this->update(['is_active' => ! $this->is_active]);
}

public function isOwnedBy(User $user): bool
{
    return $this->user_id === $user->id;
}
```

When the operation has multiple steps or side effects — move it to an Action. See `rules/actions.md`.

## Scope Pattern

```php
// Wrong — hardcoded auth, breaks in queues
public function scopeForCurrentUser(Builder $q): Builder
{
    return $q->where('user_id', auth()->id());
}

// Correct
public function scopeForUser(Builder $q, ?int $userId = null): Builder
{
    return $q->where('user_id', $userId ?? auth()->id());
}
```

## Package Traits

| Trait | Adds |
|-------|------|
| `Filterable` | `useFilters()` scope |
| `HasActivation` | `is_active`, `activate()`, `deactivate()` |
| `HasCreatedBy` | `created_by` auto-fill from auth |
| `HasGeneratedCode` | auto-generated unique `code` column |
| `HasOrder` | `order` column with ordering helpers |
| `Sluggable` | auto-generated `slug` |

Import from `Essa\APIToolKit\Traits\{TraitName}`.
