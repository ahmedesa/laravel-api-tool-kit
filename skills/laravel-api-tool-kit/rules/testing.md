# Testing

## Rules

- MUST write a feature test for every endpoint action
- MUST use `RefreshDatabase` in every feature test
- MUST use factories for all test data — NEVER hardcode IDs or create models with `new`
- NEVER hardcode integer IDs in URL paths (e.g. `/api/cars/1`) — always use `$model->id`
- MUST assert both the response status AND the database state for write operations
- MUST test at least one failure path (unauthenticated, invalid input, forbidden)
- NEVER rely on seeded data — create exactly what the test needs
- MUST freeze time with `$this->travelTo()` in any test involving date/time logic — NEVER rely on real `now()`
- MUST hardcode expected values in assertions — NEVER reuse implementation constants in tests (masks bugs where the constant itself drifted)
- MUST fake external services in test setup — `Storage::fake('s3')`, `Mail::fake()`, `Notification::fake()`, `Event::fake()`

## Pest vs PHPUnit

Both syntaxes are valid. Match the style already used in the project.

**PHPUnit class syntax** (default):
```php
class CreateCarTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_car(): void { ... }
}
```

**Pest functional syntax**:
```php
uses(TestCase::class, RefreshDatabase::class);

test('authenticated user can create a car', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/cars', ['name' => 'Tesla', 'color' => 'red'])
        ->assertCreated();

    $this->assertDatabaseHas('cars', ['name' => 'Tesla']);
});
```

Pest helper functions (`createUser()`, `createAdmin()`) go in `tests/Pest.php` or trait files composed via `uses()`.

## Feature Test Structure

```php
class CreateCarTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_car(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/cars', ['name' => 'Tesla', 'color' => 'red']);

        $response->assertCreated();
        $this->assertDatabaseHas('cars', ['name' => 'Tesla', 'user_id' => $user->id]);
    }

    public function test_unauthenticated_user_cannot_create_a_car(): void
    {
        $this->postJson('/api/cars', ['name' => 'Tesla'])->assertUnauthorized();
    }

    public function test_name_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/cars', ['color' => 'red'])
            ->assertUnprocessable();
    }
}
```

## Asserting Response Structure

For read operations, assert the JSON shape in addition to the status:

```php
public function test_authenticated_user_can_list_cars(): void
{
    $user = User::factory()->create();
    Car::factory()->count(3)->for($user)->create();

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/cars');

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'name', 'color', 'is_active', 'created_at'],
        ],
    ]);
}

public function test_authenticated_user_can_get_a_car(): void
{
    $user = User::factory()->create();
    $car  = Car::factory()->for($user)->create();

    $response = $this->actingAs($user, 'sanctum')->getJson("/api/cars/{$car->id}");

    $response->assertOk();
    $response->assertJsonFragment(['id' => $car->id, 'name' => $car->name]);
}
```

## File Structure

```
tests/Feature/
└── Car/
    ├── CreateCarTest.php
    ├── UpdateCarTest.php
    ├── DeleteCarTest.php
    ├── GetCarTest.php
    └── ListCarsTest.php
```
