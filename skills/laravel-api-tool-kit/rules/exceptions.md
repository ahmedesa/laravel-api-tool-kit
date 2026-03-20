# Exception Handling

## Rules

- MUST throw `UnprocessableEntityHttpException` for all business rule violations
- MUST use route model binding for single-model lookups — NEVER `find($id)` with manual 404
- NEVER use `abort()` or `abort_if()` for business logic
- NEVER swallow exceptions with empty `catch` blocks
- NEVER catch an exception just to re-throw a generic one
- Services and Actions MUST throw exceptions — NEVER return error response objects

## Business Rule Violations

Business logic belongs in Actions, not controllers. Throw from the Action — the exception handler converts it to the correct HTTP response automatically.

```php
// In an Action or Service — always throw, never return a response:
if ($user->hasReachedCarLimit()) {
    throw new UnprocessableEntityHttpException(trans('car.limit_reached'));
}

// In a controller — only for infrastructure-level guards (e.g., feature flag off),
// not for domain rules. Use responseUnprocessable() only when there is truly no Action involved:
return $this->responseUnprocessable(trans('car.limit_reached'));
```

## Package-Handled Exceptions

The package automatically converts these exceptions to the correct JSON response. Always throw the right one — never build the response manually.

| Exception | HTTP Status | When to throw |
|-----------|-------------|---------------|
| `UnprocessableEntityHttpException` | 422 | Business rule violation (limit reached, invalid state, precondition failed) |
| `BadRequestHttpException` | 400 | Malformed or structurally invalid request that passed validation |
| `ConflictHttpException` | 409 | Resource conflict (duplicate entry, concurrent modification) |
| `NotAcceptableHttpException` | 406 | Precondition not met (feature disabled, unsupported operation) |
| `AuthorizationException` | 403 | Auto-thrown by `$this->authorize()` — NEVER throw manually, let the policy handle it |
| `NotFoundHttpException` | 404 | Auto-thrown by route model binding — rarely throw manually |

```php
// Business rule violation
throw new UnprocessableEntityHttpException(trans('car.limit_reached'));

// Duplicate / conflict
throw new ConflictHttpException(trans('car.already_exists'));

// Feature disabled or precondition not met
throw new NotAcceptableHttpException(trans('car.feature_disabled'));

// Bad request structure
throw new BadRequestHttpException(trans('car.invalid_format'));
```

## Not Found — Use Route Model Binding

```php
// Wrong
public function show(string $id): JsonResponse
{
    $car = Car::find($id);
    if (! $car) {
        return $this->responseNotFound();
    }
}

// Correct
public function show(Car $car): JsonResponse
{
    return $this->responseSuccess(null, new CarResource($car));
}
```
