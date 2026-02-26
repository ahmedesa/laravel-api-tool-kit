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
