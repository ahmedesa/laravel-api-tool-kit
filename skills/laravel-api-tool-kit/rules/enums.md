# Enums

## Rules

- MUST be PHP backed enums (string or int) — NEVER pure enums for database values
- MUST use the `EnumHelpers` trait from `Essa\APIToolKit\Enum\EnumHelpers`
- MUST cast enum columns in the model's `$casts` array
- MUST use `Rule::in(YourEnum::values())` in validation — NEVER hardcode allowed values
- NEVER compare with raw strings — use the enum case: `$car->status === CarStatusEnum::ACTIVE`

## Structure

```php
enum CarStatusEnum: string
{
    use EnumHelpers;

    case PENDING  = 'pending';
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
}
```

## EnumHelpers Methods

| Method | Returns |
|--------|---------|
| `values()` | `['pending', 'active', ...]` |
| `names()` | `['Pending', 'Active', ...]` |
| `isValid($v)` | `bool` |
| `fromValue($v)` | enum case |

## In Model

```php
protected $casts = ['status' => CarStatusEnum::class];
```

## In Validation

```php
'status' => ['required', Rule::in(CarStatusEnum::values())],
```

## In Resource

```php
'status' => $this->status->value,
```

## Strategy for Magic Values

If a value carries business meaning or is used in multiple places, extract it.

| Scenario | Action |
|----------|--------|
| New status or type needed | **CREATE** a new Backed Enum (e.g. `PostStatusEnum`) |
| New storage path needed | **CREATE** a `StoragePaths` Backed Enum |
| Role or Permission name | **CREATE** a `RoleName` or `Ability` Backed Enum |
| Shared numeric threshold or size | **CREATE** a Backed Enum (e.g. `UtilizationThresholds: int`) |
| Cache / session / cookie key | **CREATE** a Backed Enum with `key()` + `ttl()` methods |

**Rule for AI**: Do not wait for the user to ask for an Enum or Constant. If you are about to type a raw value (string, number, or boolean) that represents business logic or configuration, STOP and extract it. Always use a Backed Enum for any shared value. Only use `private const` on the class itself for single-use values that are not shared anywhere else.
