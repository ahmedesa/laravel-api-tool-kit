# Database

## Rules

- MUST follow the project's primary key convention (see SKILL.md Project Defaults) — if ULID: `ulid('id')->primary()` + `foreignUlid()` + `HasUlids` trait; if auto-increment: `id()` + `foreignId()`
- MUST wrap any operation writing to multiple tables in `DB::transaction()`
- MUST eager load relationships before loops or pagination — NEVER lazy load in loops
- MUST use `whereIn()->update()` for bulk updates — NEVER loop with individual saves
- MUST use `insert()` for bulk inserts — NEVER loop with individual creates
- MUST use `chunk()` when processing large datasets — NEVER load entire tables with `->get()`
- MUST use `increment()` / `decrement()` for counter columns — NEVER read-modify-save

## Migration Structure

```php
// If project uses ULIDs (check SKILL.md Project Defaults)
Schema::create('cars', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->string('name');
    $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});

// If project uses auto-increment
Schema::create('cars', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});
```

## Transactions

```php
DB::transaction(function () use ($data): void {
    $car = Car::create($data);
    $car->features()->attach($data['feature_ids']);
    CarCreated::dispatch($car);
});
```

## Bulk Operations

```php
// Bulk update
Car::whereIn('id', $ids)->update(['is_active' => false]);

// Atomic counter
$car->increment('views_count');

// Chunk large datasets
Car::whereNull('processed_at')->chunk(200, function ($cars): void {
    foreach ($cars as $car) {
        $car->process();
    }
});
```
