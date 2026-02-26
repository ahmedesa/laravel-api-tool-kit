# Workflow: Add a New CRUD Endpoint

Follow these steps in order. Do not skip steps or reorder them.

---

## Step 0 — Gather Requirements

Before creating any file, confirm:
- What is the resource name? (e.g. `Car`)
- What columns does it have and what are their types?
- Does it need soft deletes?
- Does it need file uploads?
- Which routes are needed? (all CRUD, or subset?)
- Is authentication required? Which guard?

---

## Step 1 — Model

Follow `rules/models.md` for the correct structure.

- Add all columns to `$fillable`
- Add `$casts` for booleans, enums, and arrays
- Add `SoftDeletes` if needed
- Add relationships
- Do NOT bind `$default_filters` yet — do that after creating the Filter (Step 3)

File: `app/Models/Car.php`

---

## Step 2 — Migration

Create a standard Laravel migration:

Use the project's primary key convention from SKILL.md Project Defaults:

```php
// ULID project
Schema::create('cars', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->string('name');
    $table->string('color');
    $table->boolean('is_active')->default(true);
    $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});

// Auto-increment project
Schema::create('cars', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('color');
    $table->boolean('is_active')->default(true);
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});
```

---

## Step 3 — Filter

Follow `rules/filters.md` for the correct structure.

- Populate `$allowedFilters` with columns clients can filter by equality
- Populate `$allowedSorts` with sortable columns (always include `created_at`)
- Populate `$columnSearch` if text search is needed
- Add `$allowedIncludes` for relationships clients can eager-load
- Add custom filter methods for non-trivial conditions

File: `app/Filters/CarFilters.php`

Then go back to the Model and bind it:
```php
protected string $default_filters = CarFilters::class;
```

---

## Step 4 — Enum (if needed)

Follow `rules/enums.md` for the correct structure. Create one for any fixed-value column (status, type, etc.).

File: `app/Enums/CarStatusEnum.php`

Cast it in the Model:
```php
protected $casts = [
    'status' => CarStatusEnum::class,
];
```

Use it in validation:
```php
'status' => ['required', Rule::in(CarStatusEnum::values())],
```

---

## Step 5 — Form Requests

Follow `rules/requests.md` for the correct structure.

- Create request: required rules
- Update request: same rules but with `sometimes` prefix for partial updates

Files:
```
app/Http/Requests/Car/CreateCarRequest.php
app/Http/Requests/Car/UpdateCarRequest.php
```

---

## Step 6 — Resource

Follow `rules/resources.md` for the correct structure.

- Add all scalar fields to the array
- Use `$this->whenLoaded('relation')` for every relationship
- Use `dateTimeFormat()` for all timestamps
- Never add raw DB queries inside `toArray()`

File: `app/Http/Resources/Car/CarResource.php`

---

## Step 7 — Action (only if needed)

Follow `rules/actions.md` for the correct structure. Create one only if the operation:
- Has multiple distinct steps (even if all on the same model)
- Calls an external service (file upload, email, payment)
- Needs to be reused across multiple controllers

If it's a simple `Model::create($data)` — do it directly in the controller. No Action needed.

For external 3rd-party integrations (SMS, payment, etc.), see `rules/services.md`.

File: `app/Actions/CreateCarAction.php`

---

## Step 8 — Controller

Follow `rules/controllers.md` for the correct structure. Use the pattern with a constructor-injected Action/Service/Repository if you created one in Step 7.

- Replace `{Model}` with the resource name
- Add middleware in the constructor
- Add `$this->authorize()` on methods that need policy checks

File: `app/Http/Controllers/CarController.php`

---

## Step 9 — Route

Register the resource route in `routes/api.php`:

```php
// Full CRUD
Route::apiResource('cars', CarController::class)->middleware('auth:sanctum');

// Or manual registration for partial CRUD
Route::middleware('auth:sanctum')->group(function () {
    Route::get('cars', [CarController::class, 'index']);
    Route::post('cars', [CarController::class, 'store'])->middleware('throttle:10,1');
    Route::get('cars/{car}', [CarController::class, 'show']);
    Route::put('cars/{car}', [CarController::class, 'update']);
    Route::delete('cars/{car}', [CarController::class, 'destroy']);
});
```

---

## Step 10 — Factory & Test (recommended)

Create a factory:
```php
// database/factories/CarFactory.php
public function definition(): array
{
    return [
        'name'      => fake()->word(),
        'color'     => fake()->safeColorName(),
        'is_active' => true,
        'user_id'   => User::factory(),
    ];
}
```

Write a feature test covering index, store, show, update, destroy.

---

## Checklist

Before marking the feature done, verify:

- [ ] `$fillable` is populated
- [ ] `$casts` covers all booleans and enums
- [ ] Filter class has meaningful `$allowedFilters` and `$allowedSorts`
- [ ] All controller methods use `$request->validated()`
- [ ] All controller messages use `trans()`
- [ ] All resource timestamps use `dateTimeFormat()`
- [ ] All resource relationships use `whenLoaded()`
- [ ] Routes have appropriate middleware (auth, throttle)
- [ ] `declare(strict_types=1)` on every new file
- [ ] Every method has parameter types and return types
