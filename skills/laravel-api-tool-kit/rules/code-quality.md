# Code Quality

## Every File

- MUST start with `declare(strict_types=1);` immediately after `<?php`
- MUST declare parameter types and return types on every method — use `void` if nothing is returned
- MUST use `?Type` for nullable parameters — NEVER use `mixed` unless genuinely unavoidable
- NEVER use nested ternaries — use `match` instead
- NEVER hardcode business-meaning strings or numbers — use enums, constants, or config values (see table below)
- NEVER commit commented-out code
- NEVER call `env()` in application code — only inside `config/*.php` files
- NEVER use fully qualified class names (e.g. `\App\Domain\User\Models\User`) in code logic, model casts, or policy parameters — ALWAYS use `use` imports at the top and the short class name

## No Hardcoded Values — Total Elimination

Every repeated or business-meaning value (string, number, or boolean) MUST have a name. NEVER use magic values. Extract them immediately.

| Value type | Where to put it |
|------------|-----------------|
| Domain status / type | Backed Enum — see `rules/enums.md` |
| Storage / Media paths | `StoragePaths` Enum (e.g. `case PETS = 'pets'`) |
| Role / Permission names | `RoleName` or `Ability` Enum |
| Cache / session / cookie key | Backed Enum with `key()` + `ttl()` — see below |
| Config-level threshold / size | Backed Enum (e.g. `UtilizationThresholds::HIGH->value`) |
| Single-use class constant | `private const` on the class itself |

Benefits: single source of truth, safe rename via IDE, full-codebase search finds all usages.

## Constructor Dependencies

- MUST use `private readonly` property promotion for all injected dependencies

## Naming

| Item | Convention |
|------|-----------|
| Classes | PascalCase |
| Methods / variables | camelCase |
| Constants | SCREAMING_SNAKE_CASE |
| DB columns | snake_case |

## Match Over Nested Ternary

```php
$label = match($car->status) {
    CarStatusEnum::ACTIVE  => 'Active',
    CarStatusEnum::PENDING => 'Pending',
    default                => 'Unknown',
};
```

## Method Ordering

Constructor → public methods → protected methods → private methods.

## Early Return

```php
if (! $car->isActive()) {
    return;
}
if (! $car->hasOwner()) {
    return;
}
// main logic
```

## Collections Over Manual Loops

```php
$activeIds = collect($cars)
    ->filter(fn(Car $car) => $car->is_active)
    ->pluck('id')
    ->all();
```

## Constants for Magic Numbers

Place single-use constants directly on the class. Extract shared values into a Backed Enum:

```php
// Single-use — private const on the class
class CloseStoreAction
{
    private const HIGH_UTILIZATION_THRESHOLD = 80;
}

// Shared — Backed Enum (app/Enums/)
enum UtilizationThresholds: int
{
    case HIGH = 80;
    case MAX_BATCH_SIZE = 500;
}

// Usage
if ($value > UtilizationThresholds::HIGH->value) { ... }
```

## Key Constants (Cache / Session / Cookies)

NEVER use inline strings for cache, session, or cookie keys. Use a backed **Enum** so each key carries its own TTL and a typed `key()` method:

```php
namespace App\Enums\Cache;

enum CacheKeysConstants: string
{
    case USER_PROFILE     = 'user:%s:profile';
    case USER_PERMISSIONS = 'user:%s:permissions';
    case PRODUCT_STOCK    = 'product:%s:stock';

    public function ttl(): int
    {
        return match ($this) {
            self::USER_PERMISSIONS => 300,
            default                => 900,
        };
    }

    public function key(mixed ...$args): string
    {
        return sprintf($this->value, ...$args);
    }
}
```

```php
// Usage
Cache::remember(
    CacheKeysConstants::USER_PROFILE->key($userId),
    CacheKeysConstants::USER_PROFILE->ttl(),
    fn() => $user->fresh()
);
```

Benefits over a plain class: each key owns its TTL, `->key()` is typed and IDE-auto-completable, adding a new key never forgets its TTL.
