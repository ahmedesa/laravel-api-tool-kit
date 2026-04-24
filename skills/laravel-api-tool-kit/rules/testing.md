# Testing

## Philosophy

Test from the user's entry point. HTTP endpoints → feature tests. Background jobs → execute the job directly. Drop to a unit test only when forced to by an external dependency or a side effect invisible from the entry point. This produces tests that survive refactoring and catch the integration bugs unit tests miss.

## Pick the Test Type

| Trigger | Test type |
|---|---|
| HTTP endpoint | Feature test (default) |
| Background job or console command | Execute directly, assert DB state |
| External service (payment, AI, SMS, push) | Unit test, mock the interface |
| Side effect invisible from HTTP (event fired once, credit restored, idempotency) | Unit test for the Action |
| Pure logic with no DB (calculator, rule evaluator) | Unit test |

When in doubt, feature test.

## Testing Jobs

When the entry point is a job, extract a reusable helper that resolves dependencies and calls `handle()`. Every job test calls the helper — never the job constructor directly:

```php
protected function executeJobDirectly(OpCity $opCity, array $zoneUtils): void
{
    $job = new ClosuresRulesMonitorJob(opCityId: $opCity->id, zoneUtils: collect($zoneUtils));
    $job->handle(app(ClosureRulesRepo::class), app(Orchestrator::class));
}
```

Keeps dependency wiring in one place.

## Writing Unit Tests

When the decision tree sends you to a unit test:

**External service** — bind an anonymous implementation, avoid Mockery:
```php
$this->app->bind(AIGeneratorInterface::class, fn () => new class implements AIGeneratorInterface {
    public function generate(RequestData $data): ResponseData {
        throw new RuntimeException('AI unavailable');
    }
});
```

**Invisible side effect** (idempotency, credit restoration, event precision) — still uses `RefreshDatabase`; the "unit" is behavior, not DB isolation:
```php
Event::fake([ContentReported::class]);
app(CreateReportAction::class)->execute($user, $data);
Event::assertDispatchedTimes(ContentReported::class, 1);
```

**Pure logic** — no framework, no DB:
```php
expect((new PriceCalculator)->total($items))->toBe(150);
```

## Core Rules

- MUST write a feature test for every endpoint
- MUST use `RefreshDatabase` and factories — never hardcoded IDs, never seeded data
- MUST assert both status AND database state for writes
- MUST test at least one failure path (unauthenticated, invalid, forbidden)
- MUST freeze time with `travelTo()` **when test logic depends on time** (expiry, scheduling, duration, boundary crossings) — not for tests that just happen to create records with timestamps
- MUST hardcode expected values — never reuse implementation constants (masks bugs where the constant drifted)
- MUST fake external services: `Storage::fake()`, `Mail::fake()`, `Notification::fake()`, `Queue::fake()`
- Prefer `assertJsonPath` over `assertSee` — `assertSee` searches raw body and produces false positives

## Asserting Side Effects

Faking alone isn't enough — use closures to verify WHAT was sent:

```php
Mail::assertQueued(ActivationMail::class, fn ($mail) => $mail->code === (int) $code);

Notification::assertSentTo($user, FriendRequestSent::class,
    fn ($n, $channels) => in_array(FcmChannel::class, $channels, true));

Queue::assertPushed(ProcessOrderJob::class, fn ($job) => $job->orderId === $order->id);
```

Bare `assertQueued(SomeMail::class)` only proves something was sent — not the right thing to the right recipient.

## What NOT to Test

These are covered implicitly by feature tests — do not write dedicated tests:

- Controllers (tested through HTTP)
- Middleware (tested by 401/403 assertions)
- Resources / Transformers (tested by JSON shape assertions)
- Model relationships in isolation
- Service container bindings

A wrong relationship name throws in the feature test. A missing binding fails the action. Trust the observable failure.

## File Organization: By Concern, Not by Resource

One file per business concern. Not one giant file per resource.

```
tests/Feature/Post/
├── PostCrudTest.php             ← CRUD operations
├── PostAudienceTest.php         ← visibility rules
├── CreatePostValidationTest.php ← input validation
└── PostNotificationsTest.php    ← notification side effects
```

File-level docblocks only for complex domains — skip for simple CRUD.

## BaseTestCase Pattern

Extract auth helpers once; test files extend `BaseTestCase`, not `TestCase`:

```php
public function loginAsUser(): User
{
    $user = User::factory()->create();
    $this->actingAs($user);
    return $user;
}
```

## Infrastructure State (Redis, Cache)

Factories cover the database. For Redis, cache, or other infrastructure state, write small helpers that touch the real backend — don't mock it:

```php
protected function setActiveOrders(int $storeId, int $count): void
{
    Redis::hset("opcity:orders", $storeId, $count);
}
```

Group these next to the domain's other helpers (in a trait for complex domains).

## Multi-Actor Testing

When permissions differ by role or relationship, test all actors in **one test** — not three:

```php
$this->actingAs($friend);
$this->getJson("/api/posts/{$post->id}")->assertOk();

$this->actingAs($stranger);
$this->getJson("/api/posts/{$post->id}")->assertForbidden();
```

## Actions as Setup, Not Subjects

Use actions to reach state, not as the thing under test:

```php
// ✓ Setup — HTTP is what's tested
app(SendFriendRequestAction::class)->execute($requester, $user);
$this->actingAs($user)->putJson("/api/friends/{$requester->id}/accept")->assertOk();
```

Exception: unit test an action when its side effects (events, external calls) are invisible from HTTP.

## State Transitions

Test what happens when state changes after setup:

```php
$this->postJson('/api/conversations', [...])->assertCreated();  // while public

$user->update(['visibility' => 'private']);

$this->postJson(...)->assertForbidden();  // same action now blocked
```

## Assert Computed Values Exactly

For business calculations, assert the number — not just existence:

```php
// ✗ Weak
$this->assertDatabaseHas('rule_logs', ['store_id' => $store->id]);

// ✓ Strong
expect($log->from->diffInMinutes($log->to))->toBe(60);
```

## The `$model->refresh()` Pattern

When you need to prove a **specific** record was mutated (by a job, action, event, or listener) — hold the reference before, refresh after:

```php
$log = $this->createLog(['live' => true]);
$this->executeJobDirectly();
$log->refresh();
expect($log->live)->toBeFalse();  // proves THIS record changed, not that some matching row exists
```

## Boundary Values

Test three points: at the limit, one above, and zero/null (usually means disabled):

```php
$this->setActiveOrders(5);   // limit is 5 → should NOT trigger
$this->setActiveOrders(6);   // → SHOULD trigger
$store->update(['limit' => 0]);  // disabled → should never trigger
```

Also test **optional configuration set to null** — catches fallback bugs when a feature is partially configured:

```php
$rule = $this->createRule(['second_offense_duration' => null]);
// trigger scenario — should fall back to first-offense flow, not crash
expect($scheduledClosing)->toBeNull();
```

## Safe Instants for Periodic Logic

For logic tied to recurring boundaries (daily resets, timezone rollovers, cron schedules), pin time to a moment **away from the boundary** — otherwise tests flake when the real clock drifts near the boundary:

```php
// Daily reset fires at 05:00 UTC — pin to noon to stay inside one window
$this->travelTo(Carbon::parse('2025-06-15 12:00:00', 'UTC'));
```

Extract to a named helper when shared across a file: `travelToSafeInstant()`. The name signals intent — future readers know WHY this specific timestamp was chosen.

## Assertion Helpers

Extract when repeated 2+ times, with descriptive failure messages:

```php
protected function assertRuleLogCreated(int $ruleId): void
{
    $log = RuleLog::where('rule_id', $ruleId)->where('live', true)->first();
    Assert::assertNotNull($log, "Rule log should exist for rule {$ruleId}");
}
```

## Domain Test Helpers Trait

For complex domains spanning multiple test files, combine ALL helpers into ONE trait composed via `uses()`:

```php
trait ClosureRulesHelpers
{
    protected function createClosureRule(array $attrs = []): ClosuresRule { /* factory */ }
    protected function setActiveOrders(int $storeId, int $count): void      { /* Redis */ }
    protected function executeJobDirectly(OpCity $opCity): void             { /* job */ }
    protected function assertRuleLogCreated(int $ruleId): void              { /* assert */ }
}
```

Combines factories + infrastructure setup + execution + custom assertions. One `uses()`, full toolkit.

## Comments in Tests

Only when the expected outcome needs explanation — non-obvious timing, "why this number", or a business rule the test name can't convey. Never what the code already says.

```php
// exclude_store_ids wins over store_ids when both contain the same store
expect($log)->toBeNull();
```

## Pest vs PHPUnit

**Pest** (modern Laravel default):
```php
uses(BaseTestCase::class);

test('user can create a car', function () {
    $user = $this->loginAsUser();

    $this->postJson('/api/cars', ['name' => 'Tesla'])->assertCreated();

    $this->assertDatabaseHas('cars', ['name' => 'Tesla', 'user_id' => $user->id]);
});
```

**PHPUnit** — use only if the project already uses it:
```php
class CreateCarTest extends BaseTestCase
{
    public function test_user_can_create_a_car(): void { /* ... */ }
}
```

Match whatever the project already uses. Never mix both in the same codebase.
