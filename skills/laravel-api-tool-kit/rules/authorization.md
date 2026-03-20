# Authorization

All model-level authorization MUST live in Policies — centralized, reusable, keeps controllers clean.

## Rules

- MUST put all authorization logic in Policies — NEVER inline in controllers
- MUST call `$this->authorize('action', $model)` before any write operation in controllers
- `authorize()` in FormRequests MUST return `true` — real auth goes in Policies, not here
- NEVER use `abort(403)`, `Gate::allows()`, or manual `if ($user->id !== $model->user_id)` checks in controllers
- Policy methods MUST return `bool` — no side effects, no DB writes

## Policy Structure

```php
class CarPolicy
{
    // viewAny — guards the index endpoint (listing all records)
    public function viewAny(User $user): bool
    {
        return true; // or: $user->hasRole('admin')
    }

    // view — guards the show endpoint (single record)
    public function view(User $user, Car $car): bool
    {
        return $car->user_id === $user->id;
    }

    // create — guards the store endpoint
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Car $car): bool
    {
        return $car->user_id === $user->id;
    }

    public function delete(User $user, Car $car): bool
    {
        return $car->user_id === $user->id;
    }
}
```

## Controller Usage

```php
public function index(): AnonymousResourceCollection
{
    $this->authorize('viewAny', Car::class);
    return CarResource::collection(Car::useFilters()->dynamicPaginate());
}

public function update(UpdateCarRequest $request, Car $car): JsonResponse
{
    $this->authorize('update', $car);
    $car->update($request->validated());
    return $this->responseSuccess(trans('car.updated'), new CarResource($car));
}
```
