# Anti-Patterns

Check this file before writing any code. These are the most common mistakes.

---

## Code Quality

**NEVER omit `declare(strict_types=1)`**
```php
// Wrong — missing declaration
<?php

namespace App\Http\Controllers;

// Correct
<?php

declare(strict_types=1);

namespace App\Http\Controllers;
```

**NEVER omit return types or parameter types**
```php
// Wrong
public function store($request)
{
    $car = Car::create($request->all());
}

// Correct
public function store(CreateCarRequest $request): JsonResponse
{
    $car = Car::create($request->validated());
}
```

**NEVER use nested ternaries**
```php
// Wrong
$label = $s === 'active' ? 'Active' : ($s === 'pending' ? 'Pending' : 'Unknown');

// Correct
$label = match($s) {
    'active'  => 'Active',
    'pending' => 'Pending',
    default   => 'Unknown',
};
```

---

## Input & Validation

**NEVER use `$request->all()` or `$request->except()`**
```php
// Wrong
$car = Car::create($request->all());

// Correct
$car = Car::create($request->validated());
```

**NEVER validate inside a controller**
```php
// Wrong
public function store(Request $request): JsonResponse
{
    $request->validate(['name' => 'required']);
}

// Correct — use a FormRequest
public function store(CreateCarRequest $request): JsonResponse { }
```

---

## Responses

**NEVER use `response()->json()` directly**
```php
// Wrong
return response()->json(['data' => $car], 200);

// Correct
return $this->responseSuccess(null, new CarResource($car));
```

**NEVER use `abort()` for business logic**
```php
// Wrong
abort(422, 'Car limit reached');

// Correct
throw new UnprocessableEntityHttpException(trans('car.limit_reached'));
```

**NEVER hardcode user-facing strings**
```php
// Wrong
return $this->responseCreated('Car created successfully', new CarResource($car));

// Correct
return $this->responseCreated(trans('car.created'), new CarResource($car));
```

**NEVER return a raw model**
```php
// Wrong
return $this->responseSuccess(null, $car);

// Correct
return $this->responseSuccess(null, new CarResource($car));
```

---

## N+1 Queries

**NEVER access a relationship in a Resource without `whenLoaded()`**
```php
// Wrong
'owner' => $this->owner->name, // N+1

// Correct
'owner' => new UserResource($this->whenLoaded('owner')),
```

**NEVER loop with individual updates or inserts**
```php
// Wrong
foreach ($ids as $id) {
    Car::find($id)->update(['is_active' => false]);
}

// Correct
Car::whereIn('id', $ids)->update(['is_active' => false]);
```

---

## Architecture

**NEVER put business logic in controllers**
```php
// Wrong
public function store(CreateCarRequest $request): JsonResponse
{
    if ($request->user()->cars()->count() >= 5) { ... }
    // ... more logic
}

// Correct — move to an Action or model method
```

**NEVER put query/filter logic in controllers**
```php
// Wrong
if ($request->color) {
    $query->where('color', $request->color);
}

// Correct — use a Filter class
return CarResource::collection(Car::useFilters()->dynamicPaginate());
```

**NEVER use `->paginate()` or `->get()` on list endpoints**
```php
// Wrong
$cars = Car::paginate(15);

// Correct
$cars = Car::useFilters()->dynamicPaginate();
```

---

## Dependency Injection

**NEVER instantiate services or actions with `new`**
```php
// Wrong
public function store(CreateCarRequest $request): JsonResponse
{
    $action = new CreateCarAction();
    $car = $action->execute($request->validated());
}

// Correct — inject via constructor
public function __construct(
    private readonly CreateCarAction $createCar,
) {}
```

---

## Events

**NEVER use string-based events**
```php
// Wrong
event('car.created', $car);

// Correct
CarCreated::dispatch($car);
```

**NEVER make event properties public**
```php
// Wrong
class CarCreated
{
    public Car $car; // exposed directly
}

// Correct
class CarCreated
{
    public function __construct(private readonly Car $car) {}
    public function getCar(): Car { return $this->car; }
}
```

---

## Authorization

**NEVER write inline authorization in controllers**
```php
// Wrong
if ($car->user_id !== auth()->id()) {
    return $this->responseUnAuthorized();
}

// Correct
$this->authorize('update', $car);
```

---

## Database

**ALWAYS follow the project's primary key convention (check SKILL.md Project Defaults)**
```php
// Wrong — ignoring the project convention
// If project uses ULID:
$table->id();

// If project uses auto-increment:
$table->ulid('id')->primary();

// Correct — match what SKILL.md Project Defaults specifies
// ULID project:
$table->ulid('id')->primary();

// Auto-increment project:
$table->id();
```

**NEVER hardcode `auth()->id()` in a model scope**
```php
// Wrong
public function scopeForCurrentUser(Builder $q): Builder
{
    return $q->where('user_id', auth()->id()); // breaks in queues
}

// Correct
public function scopeForUser(Builder $q, ?int $userId = null): Builder
{
    return $q->where('user_id', $userId ?? auth()->id());
}
```

---

## Enums

**NEVER hardcode enum values in validation**
```php
// Wrong
'status' => ['required', 'in:pending,active,inactive'],

// Correct
'status' => ['required', Rule::in(CarStatusEnum::values())],
```

---

## Testing

**NEVER hardcode IDs in tests**
```php
// Wrong
$this->getJson('/api/cars/1');

// Correct
$car = Car::factory()->create();
$this->getJson("/api/cars/{$car->id}");
```

**NEVER rely on seeded data in feature tests**
```php
// Wrong — test depends on DatabaseSeeder
// Correct — create exactly what the test needs
$car = Car::factory()->for($user)->create();
```

---

## Configuration

**NEVER call `env()` in application code**
```php
// Wrong — returns null after config:cache
$key = env('STRIPE_SECRET');

// Correct
$key = config('services.stripe.secret');
```

---

## Models

**NEVER create a scope for a simple single-column condition**
```php
// Wrong — unnecessary abstraction
public function scopePending(Builder $query): Builder
{
    return $query->where('status', 'pending');
}

// Correct — just use where() directly
Car::where('status', CarStatusEnum::PENDING)->get();
```

---

## Resources

**NEVER use inline ternaries to conditionally null a field — use `$this->when()`**
```php
// Wrong — always sends the key, just nulls the value
'email' => $request->user()->is_admin ? $this->email : null,

// Correct — omits the key entirely when condition is false
'email' => $this->when($request->user()?->is_admin, $this->email),
```
